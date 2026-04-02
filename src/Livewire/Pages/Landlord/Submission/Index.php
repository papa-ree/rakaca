<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.app')]
#[Title('Submission Management')]
class Index extends Component
{
    public function mount()
    {
        if (!auth()->user()->can('submission.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.index');
    }

    #[On('deleteItem')]
    public function deleteSubmission($id)
    {
        if (!auth()->user()->can('submission.delete')) {
            abort(403);
        }

        $submission = Submission::findOrFail($id);
        $submission->delete();

        $this->dispatch('toast', message: 'Submission deleted successfully.', type: 'success');
        $this->dispatch('paginated');
    }
}
