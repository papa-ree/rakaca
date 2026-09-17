<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Models\RakacaSubmissionUpload;
use Paparee\Rakaca\Services\TicketWorkflowService;

#[Layout('rakaca::layouts.app')]
#[Title('Upload Berkas')]
class Edit extends Component
{
    use WithFileUploads;

    public ?RakacaSubmission $submission = null;

    public array $items = [];

    public array $uploads = [];

    public function temporaryFileUploadDisk(): string
    {
        return app()->isProduction() ? 's3' : 'local';
    }

    public function mount(RakacaSubmission $submission)
    {
        if ($submission->user_uuid !== auth()->user()->uuid) {
            abort(403);
        }

        if ($submission->status !== SubmissionStatus::MenungguBerkas) {
            session()->flash('error', 'Hanya tiket dengan status Menunggu Berkas yang bisa dilengkapi.');
            $this->redirectRoute('rakaca.guest.submission.index', navigate: true);

            return;
        }

        $this->submission = $submission->load(['form', 'uploads']);
        $this->items = $submission->items['data'] ?? [];
    }

    protected function rules(): array
    {
        $rules = [
            'uploads' => 'nullable|array|max:3',
            'uploads.*' => 'file|mimes:pdf|max:5120',
        ];

        if ($this->submission && $this->submission->form && $this->submission->form->meta && isset($this->submission->form->meta['fields'])) {
            foreach ($this->submission->form->meta['fields'] as $field) {
                $type = $field['type'] ?? 'string';
                $required = $field['required'] ?? false;

                if ($type === 'file') {
                    $current = $this->items[$field['key']] ?? null;
                    $isExistingPath = is_string($current) && ! empty($current);
                    if ($required && ! $isExistingPath) {
                        $rule = 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip';
                    } elseif ($isExistingPath) {
                        $rule = is_object($current)
                            ? 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip'
                            : 'nullable|string|max:10000';
                    } else {
                        $rule = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip';
                    }
                } elseif ($type === 'select') {
                    $options = $field['options'] ?? [];
                    $rule = ! empty($options)
                        ? ($required ? 'required|string|in:'.implode(',', $options) : 'nullable|string|in:'.implode(',', $options))
                        : ($required ? 'required|string|max:10000' : 'nullable|string|max:10000');
                } else {
                    $rule = match ($type) {
                        'number' => $required ? 'required|numeric' : 'nullable|numeric',
                        'email' => $required ? 'required|email|max:255' : 'nullable|email|max:255',
                        default => $required ? 'required|string|max:10000' : 'nullable|string|max:10000',
                    };
                }

                $rules["items.{$field['key']}"] = $rule;
            }
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [];

        if ($this->submission && $this->submission->form && $this->submission->form->meta && isset($this->submission->form->meta['fields'])) {
            foreach ($this->submission->form->meta['fields'] as $field) {
                $attributes["items.{$field['key']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function saveItems()
    {
        $this->validate();

        $itemsToSave = $this->items;

        foreach ($itemsToSave as $key => $value) {
            if (is_string($value)) {
                $itemsToSave[$key] = Sanitize::text($value);
            }
        }

        foreach ($this->submission?->form?->meta['fields'] ?? [] as $field) {
            $key = $field['key'] ?? null;
            if (($field['type'] ?? null) === 'file' && isset($itemsToSave[$key]) && is_object($itemsToSave[$key])) {
                $file = $itemsToSave[$key];
                if (method_exists($file, 'store')) {
                    $itemsToSave[$key] = $file->store('rakaca-submissions', 'public');
                }
            }
        }

        $this->submission->update([
            'items' => [
                'id' => $this->submission->items['id'] ?? $this->submission->id,
                'created_at' => $this->submission->items['created_at'] ?? $this->submission->created_at->toISOString(),
                'updated_at' => now()->toISOString(),
                'data' => $itemsToSave,
            ],
        ]);

        $existingCount = $this->submission->uploads()->count();
        $remaining = 3 - $existingCount;
        foreach (array_slice($this->uploads, 0, $remaining) as $file) {
            if (method_exists($file, 'store')) {
                $path = $file->store('rakaca-submission-uploads', 'public');
                RakacaSubmissionUpload::create([
                    'rakaca_submission_id' => $this->submission->id,
                    'user_uuid' => auth()->user()->uuid,
                    'file_path' => $path,
                    'original_name' => Sanitize::text($file->getClientOriginalName()),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        $this->dispatch('toast', message: 'Data berhasil disimpan.', type: 'success');
    }

    public function submitFiles()
    {
        if ($this->submission->status !== SubmissionStatus::MenungguBerkas) {
            abort(403);
        }

        $this->saveItems();

        app(TicketWorkflowService::class, ['submission' => $this->submission])->submitFiles();

        $this->dispatch('toast', message: 'Berkas berhasil dikirim untuk review.', type: 'success');
        $this->redirectRoute('rakaca.guest.submission.index', navigate: true);
    }

    public function removeUpload(int $index): void
    {
        if (isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        }
    }

    public function deleteExistingUpload(string $uploadId): void
    {
        $upload = RakacaSubmissionUpload::where('id', $uploadId)
            ->where('rakaca_submission_id', $this->submission->id)
            ->where('user_uuid', auth()->user()->uuid)
            ->first();

        if ($upload) {
            Storage::disk('public')->delete($upload->file_path);
            $upload->delete();
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.edit');
    }
}
