<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    #[Layout('rakaca::layouts.app')]
    public function render()
    {
        return view('rakaca::livewire.pages.guest.index');
    }
}