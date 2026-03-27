<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Analytic;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Paparee\Rakaca\Models\TenantAnalytics;
use Paparee\Rakaca\Models\BaleList;

#[Layout('rakaca::layouts.app')]
#[Title('Create Analytic')]
class Create extends Component
{
    public $bale_id = '';
    public $provider = 'umami';
    public $website_id = '';
    public $domain = '';
    public $enabled = true;

    protected $rules = [
        'bale_id' => 'required|exists:bale_lists,id',
        'provider' => 'required|in:umami',
        'website_id' => 'required|uuid',
        'domain' => 'nullable|string|max:255',
        'enabled' => 'boolean',
    ];

    public function mount()
    {
        if (!auth()->user()->can('analytic.create')) {
            abort(403);
        }
    }

    #[Computed]
    public function baleInstances()
    {
        return BaleList::orderBy('name')->get();
    }

    public function save()
    {
        if (!auth()->user()->can('analytic.create')) {
            abort(403);
        }

        $this->validate();

        TenantAnalytics::create([
            'bale_id' => $this->bale_id,
            'provider' => $this->provider,
            'website_id' => $this->website_id,
            'domain' => $this->domain,
            'enabled' => $this->enabled,
        ]);

        $this->dispatch('toast', message: __('Analytic created successfully.'), type: 'success');

        return redirect()->route('rakaca.landlord.analytic.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.analytic.create');
    }
}
