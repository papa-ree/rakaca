<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Bale\Core\Services\MenuRegistry;
use Spatie\Permission\Models\Permission;

// MenuRegistry menyimpan hasil filter di cache; bersihkan agar tidak
// mewarisi menu user dari test sebelumnya (id user bisa sama).
beforeEach(fn () => cache()->flush());

/**
 * RakacaServiceProvider dulu ikut meng-include menu-guest.php secara manual,
 * padahal bale/core sudah mendaftarkannya lewat registerFromProvider().
 * Akibatnya setiap grup guest tampil dua kali di sidebar.
 */
it('tidak menggandakan grup menu guest di registry', function () {
    foreach (['guest.dashboard', 'guest.sidebar', 'select-bale'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $guest = User::factory()->create();
    $guest->givePermissionTo(['guest.dashboard', 'guest.sidebar', 'select-bale']);
    $this->actingAs($guest);

    $keys = array_map(
        fn (array $group) => $group['key'],
        app(MenuRegistry::class)->getGuestGroups()
    );

    expect(array_count_values($keys))->each->toBe(1);
});

it('menampilkan setiap item menu guest satu kali di sidebar', function () {
    foreach (['guest.dashboard', 'guest.sidebar', 'select-bale'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $guest = User::factory()->create();
    $guest->givePermissionTo(['guest.dashboard', 'guest.sidebar', 'select-bale']);

    $html = $this->actingAs($guest)->get('/guest')->getContent();

    foreach (['Daftar Pengajuan', 'Buat Pengajuan', 'Pilih Bale'] as $label) {
        expect(substr_count($html, __($label)))->toBe(1);
    }
});
