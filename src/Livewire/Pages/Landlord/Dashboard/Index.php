<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Dashboard;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};

#[Layout('rakaca::layouts.app')]
#[Title('Rakaca | Dashboard')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.landlord.dashboard.index');
    }
}