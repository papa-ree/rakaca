<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.guest')]
#[Title('My Submissions')]
class Index extends Component
{
    #[Computed]
    public function submissions()
    {
        return Submission::where('user_uuid', auth()->user()->uuid)
            ->with('form')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.index');
    }

    public function deleteSubmission($id)
    {
        $submission = Submission::where('id', $id)
            ->where('user_uuid', auth()->user()->uuid)
            ->firstOrFail();

        if ($submission->status !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status menunggu yang bisa dihapus.');
            return;
        }

        $submission->delete();

        $this->dispatch('toast', message: 'Pengajuan berhasil dihapus.', type: 'success');
    }
}
