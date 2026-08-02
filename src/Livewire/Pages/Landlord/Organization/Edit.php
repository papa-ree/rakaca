<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Organization;

use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Organization;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Organization')]
class Edit extends Component
{
    public Organization $organization;

    public $name;

    public $slug;

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:bale_organizations,slug,'.$this->organization->id,
        ];
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function mount(Organization $organization)
    {
        if (! auth()->user()->can('organization.update')) {
            abort(403);
        }

        $this->organization = $organization;
        $this->name = $organization->name;
        $this->slug = $organization->slug;
    }

    public function save()
    {
        $this->validate();

        $this->organization->update([
            'name' => $this->name,
            'slug' => $this->slug,
        ]);

        session()->flash('message', __('Organization updated successfully.'));

        return redirect()->route('rakaca.landlord.organization.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.organization.edit');
    }
}
