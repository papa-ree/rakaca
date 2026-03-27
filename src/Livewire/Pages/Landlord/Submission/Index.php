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
    use WithPagination;

    public $query = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

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

    #[Computed]
    public function submissions()
    {
        return Submission::with('service')
            ->when($this->query, function ($query) {
                $query->where('code', 'like', '%' . $this->query . '%')
                    ->orWhereHas('service', function ($q) {
                        $q->where('name', 'like', '%' . $this->query . '%');
                    });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('deleteItem')]
    public function deleteSubmission($id)
    {
        if (!auth()->user()->can('submission.delete')) {
            abort(403);
        }

        $submission = Submission::findOrFail($id);
        $submission->delete();

        session()->flash('message', 'Submission deleted successfully.');
        $this->dispatch('paginated');
    }
}
