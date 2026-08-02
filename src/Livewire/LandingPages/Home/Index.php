<?php

namespace Paparee\Rakaca\Livewire\LandingPages\Home;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('rakaca::layouts.guest')]
class Index extends Component
{
    public function render()
    {
        return view('rakaca::livewire.landing-pages.home.index');
    }
}
