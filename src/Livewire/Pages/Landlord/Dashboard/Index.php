<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('rakaca::layouts.app')]
#[Title('Rakaca | Dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.landlord.dashboard.index');
    }
}
