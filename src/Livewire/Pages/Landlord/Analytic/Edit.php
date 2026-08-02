<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Analytic;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\BaleList;
use Paparee\Rakaca\Models\TenantAnalytics;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Analytic')]
class Edit extends Component
{
    public $analyticId;

    public $bale_id;

    public $provider;

    public $website_id;

    public $domain;

    public $enabled;

    protected function rules()
    {
        return [
            'bale_id' => 'required|exists:bale_lists,id',
            'provider' => 'required|in:umami',
            'website_id' => 'required|uuid',
            'domain' => 'nullable|string|max:255',
            'enabled' => 'boolean',
        ];
    }

    public function mount($analytic)
    {
        if (! auth()->user()->can('analytic.update')) {
            abort(403);
        }

        $item = TenantAnalytics::findOrFail($analytic);
        $this->analyticId = $item->id;
        $this->bale_id = $item->bale_id;
        $this->provider = $item->provider;
        $this->website_id = $item->website_id;
        $this->domain = $item->domain;
        $this->enabled = $item->enabled;
    }

    #[Computed]
    public function baleInstances()
    {
        return BaleList::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        $item = TenantAnalytics::findOrFail($this->analyticId);
        $item->update([
            'bale_id' => $this->bale_id,
            'provider' => $this->provider,
            'website_id' => $this->website_id,
            'domain' => $this->domain,
            'enabled' => $this->enabled,
        ]);

        session()->flash('message', __('Analytic updated successfully.'));

        return redirect()->route('rakaca.landlord.analytic.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.analytic.edit');
    }
}
