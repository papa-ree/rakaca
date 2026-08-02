<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('rakaca::layouts.app')]
#[Title('Bale User Management')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-user.index');
    }
}
