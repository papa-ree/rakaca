<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Service;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};
use Paparee\Rakaca\Models\Service;
use Illuminate\Support\Str;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Service')]
class Edit extends Component
{
    public Service $service;
    public $name;
    public $slug;
    public $icon;
    public $actived;

    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:rakaca_services,slug,' . $this->service->id,
            'icon' => 'nullable|string',
            'actived' => 'boolean',
        ];
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function mount(Service $service)
    {
        if (!auth()->user()->can('service.update')) {
            abort(403);
        }

        $this->service = $service;
        $this->name = $service->name;
        $this->slug = $service->slug;
        $this->icon = $service->icon;
        $this->actived = $service->actived;
    }

    public function save()
    {
        $this->validate();

        $this->service->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'actived' => $this->actived,
        ]);

        session()->flash('message', 'Service updated successfully.');

        return redirect()->route('rakaca.landlord.service.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.service.edit');
    }
}
