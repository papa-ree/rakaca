<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Lazy]
class PendingSubmissionCard extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.pending-submission-card');
    }

    public function placeholder()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.active-service-card-skeleton');
    }

    #[Computed]
    public function pendingSubmissions()
    {
        return RakacaSubmission::whereUserUuid(auth()->user()->uuid)
            ->whereNotIn('status', array_map(fn ($s) => $s->value, [SubmissionStatus::Selesai, SubmissionStatus::Ditolak, SubmissionStatus::Dibatalkan]))
            ->count();
    }
}
