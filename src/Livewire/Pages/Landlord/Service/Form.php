<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Service;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Support\ServicePalette;

#[Layout('rakaca::layouts.app')]
class Form extends Component
{
    public ?RakacaService $service = null;

    public bool $isEdit = false;

    public string $name = '';

    public string $slug = '';

    public string $icon = '';

    public string $color = ServicePalette::DEFAULT;

    public string $description = '';

    public string $sla_days = '';

    public bool $actived = true;

    public function mount(?RakacaService $service = null): void
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
            $this->color = $service->color ?? ServicePalette::DEFAULT;
            $this->description = $service->description ?? '';
            $this->sla_days = $service->sla_days ? (string) $service->sla_days : '';
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
            'color' => ['nullable', Rule::in(ServicePalette::keys())],
            'description' => 'nullable|string|max:1000',
            'sla_days' => 'nullable|integer|min:1|max:365',
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
            'color' => $this->color,
            'description' => $this->description,
            'sla_days' => $this->sla_days === '' ? null : (int) $this->sla_days,
            'actived' => $this->actived,
        ];

        if ($this->isEdit) {
            $this->service->update($data);
            session()->flash('success', 'Service updated successfully.');
        } else {
            RakacaService::create($data);
            session()->flash('success', 'New service created successfully.');
        }

        $this->redirectRoute('rakaca.landlord.service.index', navigate: true);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.service.form', [
            'palette' => ServicePalette::TONES,
        ]);
    }
}
