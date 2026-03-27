<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Organization;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};
use Paparee\Rakaca\Models\Organization;
use Illuminate\Support\Str;

#[Layout('rakaca::layouts.app')]
#[Title('Create Organization')]
class Create extends Component
{
    public $name;
    public $slug;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'slug' => 'required|unique:bale_organizations,slug',
    ];

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function mount()
    {
        if (!auth()->user()->can('organization.create')) {
            abort(403);
        }
    }

    public function save()
    {
        $this->validate();

        Organization::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'created_by' => auth()->user()->uuid ?? auth()->user()->id,
        ]);

        session()->flash('message', __('Organization created successfully.'));

        return redirect()->route('rakaca.landlord.organization.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.organization.create');
    }
}
