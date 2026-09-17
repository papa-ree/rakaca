<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Models\RakacaSubmissionUpload;

#[Layout('rakaca::layouts.app')]
#[Title('New Submission')]
class Create extends Component
{
    use WithFileUploads;

    public ?Form $selectedForm = null;

    public string $form_id = '';

    public array $items = [];

    public array $uploads = [];

    /**
     * Temporary file upload disk.
     *
     * - Local environment  → 'local'  (server filesystem, simplest)
     * - Production (APP_ENV=production) → 's3'
     *
     * Pair this with LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK=s3 in production .env
     * so Livewire's chunk-upload endpoints also target the same disk.
     */
    public function temporaryFileUploadDisk(): string
    {
        return app()->isProduction() ? 's3' : 'local';
    }

    public function mount()
    {
        $this->items = [];

        // Pre-select via ?form_id= atau ?service_id= (dari card Ajukan di overview)
        $initialFormId = Sanitize::text((string) request()->query('form_id', ''));
        $initialServiceId = Sanitize::text((string) request()->query('service_id', ''));

        if ($initialFormId !== '' && Validator::make(['form_id' => $initialFormId], ['form_id' => 'uuid|exists:rakaca_forms,id'])->passes()) {
            $form = Form::where('actived', true)->find($initialFormId);
            if ($form) {
                $this->form_id = $form->id;
                $this->updatedFormId($this->form_id);

                return;
            }
        }

        if ($initialServiceId !== '' && Validator::make(['service_id' => $initialServiceId], ['service_id' => 'uuid|exists:rakaca_services,id'])->passes()) {
            $form = Form::where('actived', true)->where('rakaca_service_id', $initialServiceId)->first();
            if ($form) {
                $this->form_id = $form->id;
                $this->updatedFormId($this->form_id);
            }
        }
    }

    public function updatedFormId($value)
    {
        if (empty($value)) {
            $this->selectedForm = null;
            $this->items = [];

            return;
        }

        $this->selectedForm = Form::with('service')->where('actived', true)->find($value);

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            $this->items = [];
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $this->items[$field['key']] = $field['type'] === 'checkbox' ? false : '';
            }
        }
    }

    protected function rules(): array
    {
        $rules = [
            'form_id' => 'required|uuid|exists:rakaca_forms,id',
            'uploads' => 'nullable|array|max:3',
            'uploads.*' => 'file|mimes:pdf|max:5120',
        ];

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $type = $field['type'] ?? 'string';
                $required = $field['required'] ?? false;

                if ($type === 'file') {
                    $rule = $required
                        ? 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip'
                        : 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip';
                } elseif ($type === 'select') {
                    $options = $field['options'] ?? [];
                    $rule = $required
                        ? 'required|string|in:'.implode(',', $options)
                        : 'nullable|string|in:'.implode(',', $options);
                    if (empty($options)) {
                        $rule = $required ? 'required|string|max:10000' : 'nullable|string|max:10000';
                    }
                } else {
                    $rule = $required ? 'required|string|max:10000' : 'nullable|string|max:10000';
                    if ($type === 'number') {
                        $rule = $required ? 'required|numeric' : 'nullable|numeric';
                    } elseif ($type === 'email') {
                        $rule = $required ? 'required|email|max:255' : 'nullable|email|max:255';
                    }
                }

                $rules["items.{$field['key']}"] = $rule;
            }
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [
            'form_id' => __('Form'),
        ];

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $attributes["items.{$field['key']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function save()
    {
        $this->validate();

        $itemsToSave = $this->items;

        // Sanitize all string inputs
        foreach ($itemsToSave as $key => $value) {
            if (is_string($value)) {
                $itemsToSave[$key] = Sanitize::text($value);
            } elseif (is_bool($value) || is_numeric($value)) {
                // keep as is for checkbox/number
            }
        }

        // Handle file uploads from dynamic fields — store to public disk and replace with path
        foreach ($this->selectedForm?->meta['fields'] ?? [] as $field) {
            $key = $field['key'] ?? null;
            if (($field['type'] ?? null) === 'file' && isset($itemsToSave[$key]) && is_object($itemsToSave[$key])) {
                $file = $itemsToSave[$key];
                if ($file instanceof TemporaryUploadedFile) {
                    $itemsToSave[$key] = $file->store('rakaca-submissions', 'public');
                } elseif (method_exists($file, 'store')) {
                    $itemsToSave[$key] = $file->store('rakaca-submissions', 'public');
                }
            }
        }

        $submission = RakacaSubmission::create([
            'user_uuid' => auth()->user()->uuid,
            'rakaca_form_id' => $this->form_id,
            'code' => strtoupper(uniqid('sub_')),
            'status' => SubmissionStatus::MenungguBerkas->value,
            'items' => [
                'id' => Str::uuid()->toString(),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
                'data' => $itemsToSave,
            ],
        ]);

        // Handle multi-upload zone (pdf, max 3, 5MB each) — separate table
        foreach ($this->uploads as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $path = $file->store('rakaca-submission-uploads', 'public');
                RakacaSubmissionUpload::create([
                    'rakaca_submission_id' => $submission->id,
                    'user_uuid' => auth()->user()->uuid,
                    'file_path' => $path,
                    'original_name' => Sanitize::text($file->getClientOriginalName()),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        session()->flash('success', 'Pengajuan berhasil dikirim.');
        $this->redirectRoute('rakaca.guest-dashboard.index', navigate: true);
    }

    public function removeUpload(int $index): void
    {
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    public function render()
    {
        $forms = Form::with('service')->where('actived', true)->get();

        return view('rakaca::livewire.pages.guest.submission.create', [
            'forms' => $forms,
        ]);
    }
}
