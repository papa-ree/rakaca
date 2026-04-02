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

    #[On('deleteItem')]
    public function deleteBaleList($id)
    {
        if (!auth()->user()->can('bale-list.delete')) {
            abort(403);
        }

        $baleList = BaleList::findOrFail($id);
        $baleList->delete();

        $this->dispatch('toast', message: __('Bale List deleted successfully.'), type: 'success');
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
