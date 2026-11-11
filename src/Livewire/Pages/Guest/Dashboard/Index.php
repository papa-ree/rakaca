<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('rakaca::layouts.app')]
#[Title('Guest Dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.index');
    }
}
