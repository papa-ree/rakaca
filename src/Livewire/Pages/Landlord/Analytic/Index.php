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
    use WithPagination;

    public $query = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

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

    #[Computed]
    public function analytics()
    {
        return TenantAnalytics::query()
            ->when($this->query, function ($query) {
                $query->where('domain', 'like', '%' . $this->query . '%')
                    ->orWhere('provider', 'like', '%' . $this->query . '%')
                    ->orWhere('website_id', 'like', '%' . $this->query . '%');
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
    public function deleteAnalytic($id)
    {
        if (!auth()->user()->can('analytic.delete')) {
            abort(403);
        }

        $analytic = TenantAnalytics::findOrFail($id);
        $analytic->delete();

        session()->flash('message', __('Analytic deleted successfully.'));
        $this->dispatch('paginated');
    }
}
