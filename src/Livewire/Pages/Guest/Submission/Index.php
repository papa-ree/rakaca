<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Services\TicketWorkflowService;

#[Layout('rakaca::layouts.app')]
#[Title('My Submissions')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.index');
    }

    #[On('cancelSubmission')]
    public function cancelSubmission(string $id): void
    {
        $submission = RakacaSubmission::where('id', $id)
            ->where('user_uuid', auth()->user()->uuid)
            ->firstOrFail();

        $status = $submission->status instanceof SubmissionStatus
            ? $submission->status
            : SubmissionStatus::fromLegacy($submission->status);

        if (! $status->cancellableByUser()) {
            $this->dispatch('toast', message: 'Status tiket ini tidak bisa dibatalkan.', type: 'error');

            return;
        }

        try {
            app(TicketWorkflowService::class, ['submission' => $submission])->userCancel();
            $this->dispatch('toast', message: 'Tiket berhasil dibatalkan.', type: 'success');
            $this->dispatch('paginated');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }
}
