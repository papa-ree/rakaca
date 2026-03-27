<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Service;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\Service;

#[Layout('rakaca::layouts.app')]
#[Title('Service Management')]
class Index extends Component
{
    use WithPagination;

    public $query = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function mount()
    {
        if (!auth()->user()->can('service.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.service.index');
    }

    #[Computed]
    public function services()
    {
        return Service::query()
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
    public function deleteService($id)
    {
        if (!auth()->user()->can('service.delete')) {
            abort(403);
        }

        $service = Service::findOrFail($id);
        $service->delete();

        session()->flash('message', 'Service deleted successfully.');
        $this->dispatch('paginated');
    }
}
