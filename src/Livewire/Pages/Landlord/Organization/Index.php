<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Organization;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\Organization;

#[Layout('rakaca::layouts.app')]
#[Title('Organization Management')]
class Index extends Component
{
    use WithPagination;

    public $query = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function mount()
    {
        if (!auth()->user()->can('organization.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.organization.index');
    }

    #[Computed]
    public function organizations()
    {
        return Organization::query()
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
    public function deleteOrganization($id)
    {
        if (!auth()->user()->can('organization.delete')) {
            abort(403);
        }

        $organization = Organization::findOrFail($id);
        $organization->delete();

        session()->flash('message', __('Organization deleted successfully.'));
        $this->dispatch('paginated');
    }
}
