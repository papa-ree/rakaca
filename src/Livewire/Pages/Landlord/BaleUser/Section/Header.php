<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Section;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('rakaca::layouts.app')]
class Header extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-user.section.header');
    }
}
