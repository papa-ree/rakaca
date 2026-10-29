<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Bale\Core\Services\MenuRegistry;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    foreach (['dashboard', 'guest.dashboard', 'guest.sidebar'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
});

it('menampilkan tombol Mode Admin di layout guest hanya untuk pemilik permission dashboard', function () {
    $guest = User::factory()->create();
    $guest->givePermissionTo(['guest.dashboard', 'guest.sidebar']);

    $this->actingAs($guest)
        ->get('/guest')
        ->assertOk()
        ->assertDontSee('Mode Admin');

    $dual = User::factory()->create();
    $dual->givePermissionTo(['dashboard', 'guest.dashboard', 'guest.sidebar']);

    $this->actingAs($dual)
        ->get('/guest')
        ->assertOk()
        ->assertSee('Mode Admin')
        ->assertDontSee('Mode Guest');
});

it('menampilkan layout landlord tanpa sidebar guest pada area admin', function () {
    $dual = User::factory()->create();
    $dual->givePermissionTo(['dashboard', 'guest.dashboard', 'guest.sidebar']);

    $this->actingAs($dual)
        ->get('/landlord-dashboard')
        ->assertOk()
        ->assertSee('Mode Guest')
        ->assertDontSee('Mode Admin');
});

it('menyembunyikan tombol Mode Guest bila user tidak punya guest.sidebar', function () {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['dashboard']);

    $this->actingAs($admin)
        ->get('/landlord-dashboard')
        ->assertOk()
        ->assertDontSee('Mode Guest');
});

it('mendaftarkan tombol switch layout lewat MenuRegistry', function () {
    $switcher = app(MenuRegistry::class)->getSwitcher();

    expect($switcher)->not->toBeNull()
        ->and($switcher['label'])->toBe('Mode Guest')
        ->and($switcher['route'])->toBe('rakaca.guest-dashboard.index')
        ->and($switcher['permission'])->toBe('guest.sidebar');
});
