<?php

namespace Paparee\Rakaca\Livewire\SharedComponents;

use Livewire\Component;
use Livewire\Attributes\{Computed, Layout};

#[Layout('rakaca::layouts.app')]
class RakacaLandlordSidebar extends Component
{
    public function render()
    {
        return view('rakaca::livewire.shared-components.rakaca-landlord-sidebar');
    }

    #[Computed]
    public function availableMenus(): array
    {
        $menu = [
            [
                'label' => 'Dashboard',
                'url' => 'landlord-dashboard',
                'icon' => 'layout-dashboard',
            ],
        ];

        // Add User Management menu if user has permission
        if (auth()->check() && auth()->user()->can('user-management.read')) {
            $menu[] = [
                'label' => 'User Management',
                'url' => 'user-management',
                'icon' => 'users',
            ];
        }

        return $menu;
    }

    #[Computed]
    public function availableKosadataMenus(): array
    {
        $menu = [];

        $conditionalMenus = [
            \Nawasara\Kosadata\Livewire\Pages\Overview\Index::class =>
                [
                    'label' => 'Overview',
                    'url' => 'internet-desa-overview',
                    'icon' => 'activity',
                ],
            \Nawasara\Kosadata\Livewire\Pages\InternetProvider\Index::class =>
                [
                    'label' => 'Internet Provider',
                    'url' => 'internet-provider',
                    'icon' => 'globe',
                ],
            \Nawasara\Kosadata\Livewire\Pages\IspDesa\Index::class =>
                [
                    'label' => 'ISP Desa',
                    'url' => 'isp-desa',
                    'icon' => 'network',
                ],
        ];

        foreach ($conditionalMenus as $class => $item) {
            if (class_exists($class)) {
                $menu[] = $item;
            }
        }

        return $menu;
    }

}