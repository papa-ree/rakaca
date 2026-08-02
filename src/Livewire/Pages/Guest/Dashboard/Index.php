<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Service;

#[Layout('rakaca::layouts.app')]
#[Title('Guest Dashboard')]
class Index extends Component
{
    #[Computed()]
    public function totalServices()
    {
        return Service::where('actived', true)->count();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.index');
    }
}
