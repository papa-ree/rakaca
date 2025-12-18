<?php

namespace Paparee\Rakaca\Livewire\SharedComponents;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class RakacaGuestSidebar extends Component
{
    #[Layout('rakaca::layouts.app')]

    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('rakaca::livewire.shared-components.rakaca-guest-sidebar');
    }
}