<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleList;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\BaleList;

#[Layout('rakaca::layouts.app')]
#[Title('Bale List Management')]
class Index extends Component
{
    use WithPagination;

    public $query = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function mount()
    {
        if (!auth()->user()->can('bale-list.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-list.index');
    }

    #[Computed]
    public function baleLists()
    {
        return BaleList::query()
            ->with('organization')
            ->when($this->query, function ($query) {
                $query->where('name', 'like', '%' . $this->query . '%')
                    ->orWhere('slug', 'like', '%' . $this->query . '%');
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
    public function deleteBaleList($id)
    {
        if (!auth()->user()->can('bale-list.delete')) {
            abort(403);
        }

        $baleList = BaleList::findOrFail($id);
        $baleList->delete();

        session()->flash('message', __('Bale List deleted successfully.'));
        $this->dispatch('paginated');
    }

    public function selectBale(string $id)
    {
        session(['bale_active_uuid' => $id]);

        $selected_bale = BaleList::find($id);
        session(['bale_active_slug' => $selected_bale->slug]);

        return redirect()->route('bale.cms.overview');
    }
}
