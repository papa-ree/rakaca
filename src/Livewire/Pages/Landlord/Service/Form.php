<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Service;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Paparee\Rakaca\Models\Service;

#[Layout('rakaca::layouts.app')]
class Form extends Component
{
    public ?Service $service = null;

    public bool $isEdit = false;

    public string $name = '';

    public string $slug = '';

    public string $icon = '';

    public string $description = '';

    public bool $actived = true;

    public function mount(?Service $service = null): void
    {
        if ($service) {
            if (! auth()->user()->can('service.update')) {
                abort(403);
            }

            $this->isEdit = true;
            $this->service = $service;
            $this->name = $service->name;
            $this->slug = $service->slug;
            $this->icon = $service->icon ?? '';
            $this->description = $service->description ?? '';
            $this->actived = $service->actived;
        } else {
            if (! auth()->user()->can('service.create')) {
                abort(403);
            }
        }
    }

    public function updatedName(string $value): void
    {
        $this->name = Sanitize::text($value);
        $this->slug = Str::slug($this->name);
    }

    public function updatedSlug(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function updatedIcon(string $value): void
    {
        $this->icon = Sanitize::text($value);
    }

    public function updatedDescription(string $value): void
    {
        $this->description = Sanitize::text($value);
    }

    protected function rules(): array
    {
        $uniqueRule = $this->isEdit
            ? 'required|unique:rakaca_services,slug,'.$this->service->id
            : 'required|unique:rakaca_services,slug';

        return [
            'name' => 'required|min:3|max:255',
            'slug' => $uniqueRule,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'actived' => 'boolean',
        ];
    }

    public function save(): void
    {
        $this->name = Sanitize::text($this->name);
        $this->slug = Str::slug($this->slug);
        $this->icon = Sanitize::text($this->icon);
        $this->description = Sanitize::text($this->description);

        $this->validate();

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'description' => $this->description,
            'actived' => $this->actived,
        ];

        if ($this->isEdit) {
            $this->service->update($data);
            session()->flash('success', 'Service updated successfully.');
        } else {
            Service::create($data);
            session()->flash('success', 'New service created successfully.');
        }

        $this->redirectRoute('rakaca.landlord.service.index', navigate: true);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.service.form');
    }
}
