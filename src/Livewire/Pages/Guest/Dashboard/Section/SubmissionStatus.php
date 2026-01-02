<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\{Layout};
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.app')]
#[Lazy]
class SubmissionStatus extends Component
{
    public $selectedSubmissionId = null;
    public $showDetail = false;

    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.submission-status');
    }

    public function placeholder()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.submission-status-skeleton');
    }

    #[Computed()]
    public function submissions()
    {
        return Submission::with('service')
            ->whereUserUuid(auth()->user()->uuid)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function viewDetail($submissionId)
    {
        $this->selectedSubmissionId = $submissionId;
        $this->showDetail = true;
    }

    public function closeDetail()
    {
        $this->showDetail = false;
        $this->selectedSubmissionId = null;
    }

    #[On('closeDetail')]
    public function handleCloseDetail()
    {
        $this->closeDetail();
    }

    public function getServiceIcon($serviceIcon): string
    {
        return match ($serviceIcon) {
            'vps' => 'server',
            'email' => 'mail',
            'hosting' => 'cloud',
            'subdomain' => 'globe',
            'jaringan' => 'network',
            'aplikasi' => 'code',
            'cms' => 'file-text',
            'meeting' => 'video',
            'notification' => 'bell',
            default => 'box',
        };
    }

    public function getServiceColor($serviceIcon): string
    {
        return match ($serviceIcon) {
            'vps' => 'sky',
            'email' => 'green',
            'hosting' => 'yellow',
            'subdomain' => 'orange',
            'jaringan' => 'blue',
            'aplikasi' => 'purple',
            'cms' => 'emerald',
            'meeting' => 'indigo',
            'notification' => 'teal',
            default => 'gray',
        };
    }
}