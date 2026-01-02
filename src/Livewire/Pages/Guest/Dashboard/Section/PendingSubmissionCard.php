<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Livewire\Attributes\{Layout};
use Paparee\Rakaca\Models\Submission;

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
        return Submission::whereUserUuid(auth()->user()->uuid)
            ->where('status', 'pending')->count();
    }
}