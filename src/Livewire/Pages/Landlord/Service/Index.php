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

    #[On('deleteItem')]
    public function deleteService($id)
    {
        if (!auth()->user()->can('service.delete')) {
            abort(403);
        }

        $service = Service::findOrFail($id);
        $service->delete();

        $this->dispatch('toast', message: 'Service deleted successfully.', type: 'success');
        $this->dispatch('paginated');
    }
}
