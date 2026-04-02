<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Analytic;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\TenantAnalytics;

#[Layout('rakaca::layouts.app')]
#[Title('Analytic Management')]
class Index extends Component
{
    public function mount()
    {
        if (!auth()->user()->can('analytic.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.analytic.index');
    }

    #[On('deleteItem')]
    public function deleteAnalytic($id)
    {
        if (!auth()->user()->can('analytic.delete')) {
            abort(403);
        }

        $analytic = TenantAnalytics::findOrFail($id);
        $analytic->delete();

        $this->dispatch('toast', message: __('Analytic deleted successfully.'), type: 'success');
        $this->dispatch('paginated');
    }
}
