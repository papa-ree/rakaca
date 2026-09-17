<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Bale\Core\Support\Sanitize;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Services\TicketWorkflowService;

#[Layout('rakaca::layouts.app')]
#[Title('Detail Pengajuan')]
class Detail extends Component
{
    public RakacaSubmission $submission;

    public string $selectedAction = '';

    public array $resolution = [];

    public string $reason = '';

    public string $revise_note = '';

    protected function rules(): array
    {
        $minChars = config('rakaca.ticket.rejection_min_chars', 10);

        $rules = [
            'selectedAction' => 'required|in:complete,revise,reject',
            'reason' => [
                'exclude_unless:selectedAction,reject',
                'required',
                'string',
                "min:{$minChars}",
                'max:5000',
            ],
            'revise_note' => [
                'exclude_unless:selectedAction,revise',
                'required',
                'string',
                'min:5',
                'max:5000',
            ],
        ];

        $fields = $this->fields();

        if (empty($fields)) {
            $rules['resolution.manual_result'] = [
                'exclude_unless:selectedAction,complete',
                'required',
                'string',
                'max:10000',
            ];
        } else {
            foreach ($fields as $field) {
                $key = (string) ($field['key'] ?? '');
                if ($key === '') {
                    continue;
                }

                $fieldRequired = (bool) ($field['required'] ?? false);

                if (! $fieldRequired) {
                    $rules["resolution.{$key}"] = 'nullable';

                    continue;
                }

                $base = [
                    'exclude_unless:selectedAction,complete',
                    'required',
                ];

                $rules["resolution.{$key}"] = array_merge($base, match ($field['type'] ?? 'string') {
                    'number' => ['numeric'],
                    'email' => ['email', 'max:255'],
                    'date' => ['date'],
                    'select' => ! empty($field['options'])
                        ? ['string', 'in:'.implode(',', $field['options'])]
                        : ['string'],
                    'checkbox' => ['boolean'],
                    default => ['string', 'max:10000'],
                });
            }
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'selectedAction.required' => __('Pilih aksi tiket terlebih dahulu.'),
            'selectedAction.in' => __('Aksi tiket tidak valid.'),
            'reason.required' => __('Alasan penolakan wajib diisi.'),
            'reason.min' => __('Alasan penolakan minimal :min karakter.'),
            'revise_note.required' => __('Catatan revisi wajib diisi.'),
            'revise_note.min' => __('Catatan revisi minimal :min karakter.'),
            'resolution.manual_result.required' => __('Hasil layanan wajib diisi untuk menyelesaikan tiket.'),
        ];
    }

    protected function validationAttributes(): array
    {
        $attributes = [
            'selectedAction' => __('Aksi Tiket'),
            'reason' => __('Alasan Penolakan'),
            'revise_note' => __('Catatan Revisi'),
        ];

        foreach ($this->fields() as $field) {
            $attributes["resolution.{$field['key']}"] = $field['label'] ?? $field['key'];
        }

        return $attributes;
    }

    public function selectAction(string $action): void
    {
        if ($this->selectedAction === $action) {
            $this->selectedAction = '';
        } else {
            $this->selectedAction = $action;
        }

        $this->resetValidation();
    }

    public function mount(RakacaSubmission $submission): void
    {
        if (! auth()->user()->can('submission.read')) {
            abort(403);
        }

        $this->submission = $submission->load([
            'form.service',
            'user',
            'uploads',
            'response.processedBy',
            'response.resolvedBy',
        ]);

        $this->resolution = $this->submission->response?->resolution_data ?? [];
        $this->reason = $this->submission->response?->rejection_reason ?? '';
        $this->revise_note = $this->submission->response?->revise_note ?? '';
    }

    public function getStatusAttr(): SubmissionStatus
    {
        $status = $this->submission->status;

        return $status instanceof SubmissionStatus
            ? $status
            : SubmissionStatus::fromLegacy((string) $this->submission->getRawOriginal('status'));
    }

    public function fields(): array
    {
        return $this->submission->form?->response_form_schema ?? [];
    }

    public function canTakeOver(): bool
    {
        return $this->getStatusAttr() === SubmissionStatus::SiapDireview;
    }

    public function canReject(): bool
    {
        return in_array($this->getStatusAttr(), [SubmissionStatus::SiapDireview, SubmissionStatus::Diproses], true);
    }

    public function canRequestRevision(): bool
    {
        return $this->getStatusAttr() === SubmissionStatus::Diproses;
    }

    public function canComplete(): bool
    {
        return $this->getStatusAttr() === SubmissionStatus::Diproses;
    }

    public function takeOver(): void
    {
        if (! auth()->user()->can('submission.update')) {
            abort(403);
        }

        app(TicketWorkflowService::class, ['submission' => $this->submission])->takeOver();

        $this->dispatch('toast', message: __('Tiket diambil alih (diproses).'), type: 'success');
        $this->redirectRoute('rakaca.landlord.submission.detail', ['submission' => $this->submission->id], navigate: true);
    }

    public function executeAction(): void
    {
        if (! auth()->user()->can('submission.update')) {
            abort(403);
        }

        $this->validate();

        $service = app(TicketWorkflowService::class, ['submission' => $this->submission]);

        if ($this->selectedAction === 'complete') {
            if (! $this->canComplete()) {
                abort(403, __('Hanya tiket diproses yang bisa diselesaikan.'));
            }

            $resolution = [];
            foreach ($this->resolution as $key => $value) {
                $resolution[$key] = is_string($value) ? Sanitize::text($value) : $value;
            }

            $service->complete($resolution);
            $this->dispatch('toast', message: __('Tiket diselesaikan.'), type: 'success');
        } elseif ($this->selectedAction === 'revise') {
            if (! $this->canRequestRevision()) {
                abort(403, __('Hanya tiket diproses yang bisa diminta revisi.'));
            }

            $note = Sanitize::text($this->revise_note);
            $service->requestRevision($note);
            $this->dispatch('toast', message: __('Permintaan revisi dikirim.'), type: 'success');
        } elseif ($this->selectedAction === 'reject') {
            if (! $this->canReject()) {
                abort(403, __('Hanya tiket siap-direview atau diproses yang bisa ditolak.'));
            }

            $reason = Sanitize::text($this->reason);
            $service->reject($reason);
            $this->dispatch('toast', message: __('Tiket ditolak.'), type: 'success');
        }

        $this->redirectRoute('rakaca.landlord.submission.detail', ['submission' => $this->submission->id], navigate: true);
    }

    public function back(): void
    {
        $this->redirectRoute('rakaca.landlord.submission.index', navigate: true);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.detail');
    }
}
