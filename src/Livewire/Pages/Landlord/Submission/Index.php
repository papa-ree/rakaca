<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.app')]
#[Title('Submission Management')]
class Index extends Component
{
    public function mount()
    {
        if (! auth()->user()->can('submission.read')) {
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
        if (! auth()->user()->can('submission.delete')) {
            abort(403);
        }

        $submission = Submission::findOrFail($id);
        $submission->delete();

        $this->dispatch('toast', message: 'Submission deleted successfully.', type: 'success');
        $this->dispatch('paginated');
    }
}
