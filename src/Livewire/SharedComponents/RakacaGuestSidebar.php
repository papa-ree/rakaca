<?php

namespace Paparee\Rakaca\Livewire\SharedComponents;

use Bale\Core\Services\MenuRegistry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Paparee\Rakaca\Models\RakacaSubmission;

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
        // Fallback lama (jika MenuRegistry belum ada guest)
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

    #[Computed(persist: true)]
    public function menuGroups(): array
    {
        // Coba baca dari MenuRegistry (guest) yang sudah di-register via menu-guest.php
        try {
            $registry = app(MenuRegistry::class);

            // Jika ada method getGuestGroups (jika core sudah diupdate), pakai
            if (method_exists($registry, 'getGuestGroups')) {
                $groups = $registry->getGuestGroups();
                if (!empty($groups)) {
                    return $this->enrichWithBadge($groups);
                }
            }

            // Jika ada getGroupsByType atau resolveGroups, pakai
            if (method_exists($registry, 'getGroupsByType')) {
                $groups = $registry->getGroupsByType('guest');
                if (!empty($groups)) {
                    return $this->enrichWithBadge($groups);
                }
            }

            // Fallback: baca langsung menu-guest.php dan filter permission (tanpa ubah core)
            $path = __DIR__.'/../../menu-guest.php';
            if (file_exists($path)) {
                $config = include $path;
                if (is_array($config) && isset($config['groups'])) {
                    $filtered = [];
                    foreach ($config['groups'] as $group) {
                        $items = array_values(array_filter($group['items'] ?? [], function (array $item): bool {
                            if (isset($item['permission']) && $item['permission'] !== null) {
                                if (! auth()->check() || ! auth()->user()->can($item['permission'])) {
                                    return false;
                                }
                            }
                            if (isset($item['class']) && ! class_exists($item['class'])) {
                                return false;
                            }
                            return true;
                        }));
                        if (!empty($items)) {
                            $filtered[] = [
                                'key' => $group['key'] ?? 'unknown',
                                'label' => $group['label'] ?? 'Menu',
                                'icon' => $group['icon'] ?? 'box',
                                'items' => $items,
                            ];
                        }
                    }
                    if (!empty($filtered)) {
                        return $this->enrichWithBadge($filtered);
                    }
                }
            }

            // Jika semua gagal, fallback ke getTenantGroups / getLandlordGroups yang mungkin sudah ada guest type
            if (method_exists($registry, 'getTenantGroups')) {
                // Coba ambil semua groups dan filter type guest via reflection
                $ref = new \ReflectionClass($registry);
                $prop = $ref->getProperty('groups');
                $prop->setAccessible(true);
                $all = $prop->getValue($registry);
                $guestGroups = array_values(array_filter($all, fn ($g) => ($g['_type'] ?? '') === 'guest'));
                if (!empty($guestGroups)) {
                    $result = [];
                    foreach ($guestGroups as $group) {
                        $filteredItems = array_values(array_filter($group['items'] ?? [], function (array $item): bool {
                            if (isset($item['permission']) && $item['permission'] !== null) {
                                if (! auth()->check() || ! auth()->user()->can($item['permission'])) {
                                    return false;
                                }
                            }
                            return true;
                        }));
                        if (!empty($filteredItems)) {
                            $result[] = [
                                'key' => $group['key'] ?? 'unknown',
                                'label' => $group['label'] ?? 'Menu',
                                'icon' => $group['icon'] ?? 'box',
                                'items' => $filteredItems,
                            ];
                        }
                    }
                    if (!empty($result)) {
                        return $this->enrichWithBadge($result);
                    }
                }
            }
        } catch (\Throwable $e) {
            // fallback
        }

        // Fallback terakhir: pakai hardcoded availableMenus dalam format groups
        return [
            [
                'key' => 'guest-fallback',
                'label' => 'Guest',
                'icon' => 'user',
                'items' => $this->availableMenus(),
            ],
        ];
    }

    protected function enrichWithBadge(array $groups): array
    {
        // Tambah badge pending own untuk Pengajuan
        try {
            $pending = 0;
            if (auth()->check()) {
                $pending = RakacaSubmission::where('user_uuid', auth()->user()->uuid)
                    ->where('status', 'pending')
                    ->count();
            }
            foreach ($groups as &$group) {
                if (($group['key'] ?? '') === 'guest-pengajuan') {
                    foreach ($group['items'] as &$item) {
                        if (($item['url'] ?? '') === 'guest/submissions' && $pending > 0) {
                            $item['badge'] = (string) $pending;
                            $item['badgeColor'] = 'amber';
                        }
                    }
                    unset($item);
                }
            }
            unset($group);
        } catch (\Throwable $e) {
        }

        return $groups;
    }

    public function render()
    {
        return view('rakaca::livewire.shared-components.rakaca-guest-sidebar');
    }
}
