<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Services\TicketWorkflowService;

#[Layout('rakaca::layouts.app')]
#[Title('Detail Pengajuan')]
class Show extends Component
{
    public ?RakacaSubmission $submission = null;

    public function mount(RakacaSubmission $submission)
    {
        if ($submission->user_uuid !== auth()->user()->uuid) {
            abort(403);
        }

        $this->submission = $submission->load([
            'form',
            'uploads',
            'response' => fn ($q) => $q->with(['resolvedBy']),
        ]);
    }

    public function getStatusLabelAttr(): string
    {
        $status = $this->submission->status instanceof SubmissionStatus
            ? $this->submission->status
            : SubmissionStatus::fromLegacy($this->submission->status);

        return $status->label();
    }

    public function getStatusColorAttr(): string
    {
        $status = $this->submission->status instanceof SubmissionStatus
            ? $this->submission->status
            : SubmissionStatus::fromLegacy($this->submission->status);

        return $status->color();
    }

    public function cancelSubmission(): void
    {
        $status = $this->submission->status instanceof SubmissionStatus
            ? $this->submission->status
            : SubmissionStatus::fromLegacy($this->submission->status);

        if (! $status->cancellableByUser()) {
            $this->dispatch('toast', message: 'Status tiket ini tidak bisa dibatalkan.', type: 'error');

            return;
        }

        try {
            app(TicketWorkflowService::class, ['submission' => $this->submission])->userCancel();
            $this->dispatch('toast', message: 'Tiket berhasil dibatalkan.', type: 'success');
            $this->redirectRoute('rakaca.guest.submission.index', navigate: true);
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.show');
    }
}
