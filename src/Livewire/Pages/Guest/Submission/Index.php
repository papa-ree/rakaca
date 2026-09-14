<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Title('My Submissions')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.index');
    }

    #[On('deleteSubmission')]
    public function deleteSubmission($id)
    {
        $submission = RakacaSubmission::where('id', $id)
            ->where('user_uuid', auth()->user()->uuid)
            ->firstOrFail();

        if (!in_array($submission->status, ['pending', 'rejected'])) {
            $msg = $submission->status === 'ditutup' ? 'Pengajuan sudah ditutup dan tidak dapat dihapus.' : 'Hanya pengajuan menunggu atau ditolak yang bisa dihapus.';
            session()->flash('error', $msg);
            return;
        }

        $submission->delete();

        $this->dispatch('toast', message: 'Pengajuan berhasil dihapus.', type: 'success');
        $this->dispatch('paginated');
    }
}
