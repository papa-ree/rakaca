<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Organization;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Organization;

#[Layout('rakaca::layouts.app')]
#[Title('Organization Management')]
class Index extends Component
{
    public function mount()
    {
        if (! auth()->user()->can('organization.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.organization.index');
    }

    #[On('deleteItem')]
    public function deleteOrganization($id)
    {
        if (! auth()->user()->can('organization.delete')) {
            abort(403);
        }

        $organization = Organization::findOrFail($id);
        $organization->delete();

        $this->dispatch('toast', message: __('Organization deleted successfully.'), type: 'success');
        $this->dispatch('paginated');
    }
}
