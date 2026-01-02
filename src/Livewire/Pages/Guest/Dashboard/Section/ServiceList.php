<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Component;
use Livewire\Attributes\{Layout};

#[Layout('rakaca::layouts.app')]
class ServiceList extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.service-list');
    }
}