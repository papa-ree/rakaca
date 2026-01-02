<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Paparee\Rakaca\Models\Submission;

#[Lazy]
class SubmissionDetail extends Component
{
    public $submissionId;
    public ?Submission $submission = null;

    public function mount($submissionId)
    {
        $this->submissionId = $submissionId;
    }

    public function loadSubmission()
    {
        $this->submission = Submission::with('service')
            ->where('id', $this->submissionId)
            ->whereUserUuid(auth()->user()->uuid)
            ->first();
    }

    public function render()
    {
        if (!$this->submission) {
            $this->loadSubmission();
        }

        return view('rakaca::livewire.pages.guest.dashboard.section.submission-detail');
    }

    public function placeholder()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.submission-detail-skeleton');
    }

    #[On('close-modal')]
    public function closeModal()
    {
        $this->dispatch('closeDetail')->to(SubmissionStatus::class);
    }

    public function getServiceIcon($serviceSlug): string
    {
        return match ($serviceSlug) {
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

    public function getServiceColor($serviceSlug): string
    {
        return match ($serviceSlug) {
            'vps' => 'blue',
            'email' => 'green',
            'hosting' => 'purple',
            'subdomain' => 'indigo',
            'jaringan' => 'cyan',
            'aplikasi' => 'violet',
            'cms' => 'pink',
            'meeting' => 'orange',
            'notification' => 'red',
            default => 'gray',
        };
    }
}
