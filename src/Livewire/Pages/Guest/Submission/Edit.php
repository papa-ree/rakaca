<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Bale\Core\Support\Sanitize;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Models\RakacaSubmissionUpload;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Submission')]
class Edit extends Component
{
    use WithFileUploads;

    public ?RakacaSubmission $submission = null;

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

    public function mount(RakacaSubmission $submission)
    {
        if ($submission->user_uuid !== auth()->user()->uuid) {
            abort(403);
        }

        if (! in_array($submission->status, ['pending', 'rejected'])) {
            $msg = $submission->status === 'ditutup'
                ? 'Pengajuan sudah ditutup dan tidak dapat diedit.'
                : ($submission->status === 'review' ? 'Pengajuan sedang direview dan tidak dapat diedit.' : 'Hanya pengajuan dengan status menunggu atau ditolak yang bisa diedit.');
            session()->flash('error', $msg);
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
                    // For file fields, allow existing path string (nullable) or new upload
                    $current = $this->items[$field['key']] ?? null;
                    $isExistingPath = is_string($current) && !empty($current);
                    if ($required && !$isExistingPath) {
                        $rule = 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip';
                    } elseif ($isExistingPath) {
                        $rule = 'nullable|string|max:10000';
                        // If new file uploaded, it will be TemporaryUploadedFile, validate as file
                        if (is_object($current)) {
                            $rule = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip';
                        }
                    } else {
                        $rule = 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,zip';
                    }
                } elseif ($type === 'select') {
                    $options = $field['options'] ?? [];
                    if (!empty($options)) {
                        $rule = $required ? 'required|string|in:' . implode(',', $options) : 'nullable|string|in:' . implode(',', $options);
                    } else {
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
        $attributes = [];

        if ($this->submission && $this->submission->form && $this->submission->form->meta && isset($this->submission->form->meta['fields'])) {
            foreach ($this->submission->form->meta['fields'] as $field) {
                $attributes["items.{$field['key']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function save()
    {
        if (! in_array($this->submission->status, ['pending', 'rejected'])) {
            session()->flash('error', 'Pengajuan tidak dapat diperbarui pada status ini.');
            $this->redirectRoute('rakaca.guest.submission.index', navigate: true);
            return;
        }

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
                if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                    $itemsToSave[$key] = $file->store('rakaca-submissions', 'public');
                } elseif (method_exists($file, 'store')) {
                    $itemsToSave[$key] = $file->store('rakaca-submissions', 'public');
                }
            }
        }

        $wasRejected = $this->submission->status === 'rejected';

        $this->submission->update([
            'items' => [
                'id' => $this->submission->items['id'] ?? $this->submission->id,
                'created_at' => $this->submission->items['created_at'] ?? $this->submission->created_at->toISOString(),
                'updated_at' => now()->toISOString(),
                'data' => $itemsToSave,
            ],
            'status' => $wasRejected ? 'pending' : $this->submission->status,
            'admin_response' => $wasRejected ? null : $this->submission->admin_response,
        ]);

        // Handle multi-upload zone (pdf, max 3, 5MB each) — check current count
        $existingCount = $this->submission->uploads()->count();
        $remaining = 3 - $existingCount;
        foreach (array_slice($this->uploads, 0, $remaining) as $file) {
            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
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

        session()->flash('success', 'Pengajuan berhasil diperbarui.');
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
            \Illuminate\Support\Facades\Storage::disk('public')->delete($upload->file_path);
            $upload->delete();
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.edit');
    }
}
