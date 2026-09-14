<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Paparee\Rakaca\Models\RakacaService;

#[Layout('rakaca::layouts.app')]
class ServiceList extends Component
{
    #[Computed()]
    public function services()
    {
        return RakacaService::where('actived', true)
            ->with(['forms' => fn ($q) => $q->where('actived', true)])
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.service-list');
    }
}
