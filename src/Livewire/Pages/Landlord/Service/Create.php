<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Service;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};
use Paparee\Rakaca\Models\Service;
use Illuminate\Support\Str;

#[Layout('rakaca::layouts.app')]
#[Title('Create Service')]
class Create extends Component
{
    public $name = '';
    public $slug = '';
    public $icon = '';
    public $actived = true;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'slug' => 'required|unique:rakaca_services,slug',
        'icon' => 'nullable|string',
        'actived' => 'boolean',
    ];

    public function mount()
    {
        if (!auth()->user()->can('service.create')) {
            abort(403);
        }
    }

    public function save($data = null)
    {
        if (!auth()->user()->can('service.create')) {
            abort(403);
        }

        if ($data) {
            $this->name = $data['name'] ?? $this->name;
            $this->slug = $data['slug'] ?? $this->slug;
            $this->icon = $data['icon'] ?? $this->icon;
            $this->actived = (isset($data['actived']) && $data['actived'] !== 'false') ? (bool) $data['actived'] : false;
        }

        $this->validate();

        Service::create([
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'actived' => $this->actived,
        ]);

        session()->flash('success', 'New Service Created!');
        $this->redirectRoute('rakaca.landlord.service.index', navigate: true);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.service.create');
    }
}
