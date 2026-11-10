<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Facades\DB;
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
use Paparee\Rakaca\Services\TicketWorkflowService;
use Paparee\Rakaca\Support\SubmissionStorage;

#[Layout('rakaca::layouts.app')]
#[Title('New Submission')]
class Create extends Component
{
    use WithFileUploads;

    public ?Form $selectedForm = null;

    public string $form_id = '';

    public array $items = [];

    public array $uploads = [];

    /** Sumber berkas: `upload` (berkas sendiri) atau `srikandi` (surat masuk). */
    public string $source = 'upload';

    public string $nomor_naskah = '';

    public array $suratResults = [];

    public ?array $surat = null;

    public string $suratError = '';

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

    public function pilihUpload(): void
    {
        $this->source = 'upload';
        $this->resetSurat();
    }

    public function pilihSrikandi(): void
    {
        $this->source = 'srikandi';
        $this->uploads = [];
    }

    public function cariSurat(): void
    {
        $this->resetSurat();

        $nomor = trim($this->nomor_naskah);

        if ($nomor === '') {
            $this->addError('nomor_naskah', __('Nomor naskah wajib diisi.'));

            return;
        }

        // Nomor harus persis: pencarian LIKE akan membocorkan surat ke siapa pun
        // yang menebak sebagian nomor.
        $this->suratResults = DB::table('srikandi_naskah')
            ->where('sumber', 'rak-srikandi')
            ->where('nomor_naskah', $nomor)
            ->orderByDesc('tanggal_naskah')
            ->limit(3)
            ->get(['id', 'nomor_naskah', 'tanggal_naskah', 'hal', 'ringkasan', 'pengirim'])
            ->map(fn ($row) => (array) $row)
            ->all();

        if ($this->suratResults === []) {
            $this->suratError = __('Surat tidak ditemukan. Pastikan nomor naskah lengkap, atau coba beberapa saat lagi.');
        }
    }

    public function gunakanSurat(string $id): void
    {
        foreach ($this->suratResults as $row) {
            if ((string) $row['id'] === $id) {
                $this->surat = $row + [
                    'sumber' => 'rak-srikandi',
                    'dipilih_pada' => now()->toISOString(),
                ];

                return;
            }
        }
    }

    public function gantiSurat(): void
    {
        $this->resetSurat();
    }

    private function resetSurat(): void
    {
        $this->surat = null;
        $this->suratResults = [];
        $this->suratError = '';
    }

    protected function rules(): array
    {
        $rules = [
            'form_id' => 'required|uuid|exists:rakaca_forms,id',
            'source' => 'required|in:upload,srikandi',
            'nomor_naskah' => 'nullable|string|max:191',
            'uploads' => 'nullable|array|max:3',
            'uploads.*' => 'file|mimes:pdf|max:5120',
        ];

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $type = $field['type'] ?? 'string';
                $required = $field['required'] ?? false;

                if ($type === 'file') {
                    // Surat Srikandi sudah mewakili berkas, jadi field file tidak diminta.
                    if ($this->source === 'srikandi') {
                        continue;
                    }

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

        if ($this->source === 'srikandi' && ! $this->surat) {
            $this->addError('source', __('Pilih salah satu surat yang ditemukan.'));

            return;
        }

        if ($this->source === 'upload' && $this->uploads === []) {
            $this->addError('source', __('Unggah minimal satu berkas atau gunakan surat Srikandi.'));

            return;
        }

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
                    $itemsToSave[$key] = $file->store('rakaca-submissions', SubmissionStorage::disk());
                } elseif (method_exists($file, 'store')) {
                    $itemsToSave[$key] = $file->store('rakaca-submissions', SubmissionStorage::disk());
                }
            }
        }

        $items = [
            'id' => Str::uuid()->toString(),
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'data' => $itemsToSave,
        ];

        // Salinan metadata surat; baris Srikandi bisa berubah/hilang kapan saja.
        if ($this->surat) {
            $items['srikandi'] = $this->surat;
        }

        $submission = RakacaSubmission::create([
            'user_uuid' => $this->applicantUuid(),
            'rakaca_form_id' => $this->form_id,
            'code' => strtoupper(uniqid('sub_')),
            'status' => SubmissionStatus::MenungguBerkas->value,
            'items' => $items,
        ]);

        // Handle multi-upload zone (pdf, max 3, 5MB each) — separate table
        foreach ($this->uploads as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $path = $file->store('rakaca-submission-uploads', SubmissionStorage::disk());
                RakacaSubmissionUpload::create([
                    'rakaca_submission_id' => $submission->id,
                    'user_uuid' => $this->applicantUuid(),
                    'file_path' => $path,
                    'original_name' => Sanitize::text($file->getClientOriginalName()),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }
        }

        // Surat Srikandi sudah final, jadi tidak perlu tahap "lengkapi berkas".
        if ($this->surat) {
            app(TicketWorkflowService::class, ['submission' => $submission])->submitFiles();
        }

        $this->afterSave($submission);
    }

    /**
     * Pemilik pengajuan. Halaman guest memakai user yang login; halaman admin
     * meng-override agar pengajuan dibuat atas nama user terdaftar.
     */
    protected function applicantUuid(): string
    {
        return (string) auth()->user()->uuid;
    }

    protected function afterSave(RakacaSubmission $submission): void
    {
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
