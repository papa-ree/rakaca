<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\Attributes\{Layout};
use Paparee\Rakaca\Models\PersonHasService;

use Livewire\Attributes\Lazy;

#[Layout('rakaca::layouts.app')]
#[Lazy]
class ActiveServiceCard extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.active-service-card');
    }

    public function placeholder()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.active-service-card-skeleton');
    }

    #[Computed()]
    public function stats()
    {
        return PersonHasService::whereUserUuid(auth()->user()->uuid)
            ->selectRaw('count(*) as total, sum(case when actived = 1 then 1 else 0 end) as active')
            ->toBase()
            ->first();
    }

    #[Computed()]
    public function activeServiceCount()
    {
        return $this->stats?->active ?? 0;
    }

    #[Computed()]
    public function percentage()
    {
        $total = $this->stats?->total ?? 0;

        return $total > 0
            ? ($this->activeServiceCount() / $total) * 100
            : 0;
    }
}