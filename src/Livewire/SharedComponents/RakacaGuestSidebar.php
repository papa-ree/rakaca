<?php

namespace Paparee\Rakaca\Livewire\SharedComponents;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

class RakacaGuestSidebar extends Component
{
    #[Layout('rakaca::layouts.app')]
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    #[Computed]
    public function availableMenus(): array
    {
        $menu = [
            [
                'label' => __('Dashboard'),
                'url' => 'guest',
                'icon' => 'layout-dashboard',
            ],
        ];

        if ($this->user->hasService('bale-cms')) {
            $menu[] = [
                'label' => __('Bale CMS'),
                'url' => 'select-bale',
                'icon' => 'layers',
            ];
        }

        if ($this->user->hasService('wago')) {
            $menu[] = [
                'label' => __('Wago'),
                'url' => 'select-bale',
                'icon' => 'message-square',
            ];
        }

        return $menu;
    }

    public function render()
    {
        return view('rakaca::livewire.shared-components.rakaca-guest-sidebar');
    }
}
