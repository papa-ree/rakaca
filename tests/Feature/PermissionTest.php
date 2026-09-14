<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    foreach ([
        'service.read', 'form.read', 'submission.read', 'personal-service.read',
        'organization.read', 'bale-list.read', 'bale-user.read', 'analytic.read',
        'guest.dashboard', 'guest.sidebar', 'dashboard',
    ] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
});

it('menolak akses landlord route tanpa permission', function () {
    $user = User::factory()->create();

    // Without service.read, should be 403 for /rakaca/services
    $this->actingAs($user)
        ->get('/rakaca/services')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/rakaca/forms')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/rakaca/submissions')
        ->assertForbidden();

    $this->actingAs($user)
        ->get('/landlord-dashboard')
        ->assertForbidden();
});

it('mengizinkan akses landlord route dengan permission yang benar', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['service.read', 'form.read', 'submission.read', 'dashboard']);

    $this->actingAs($user)
        ->get('/rakaca/services')
        ->assertOk();

    $this->actingAs($user)
        ->get('/rakaca/forms')
        ->assertOk();

    $this->actingAs($user)
        ->get('/landlord-dashboard')
        ->assertOk();
});

it('guest sidebar hanya untuk role guest', function () {
    // Create guest permission
    $guestPerm = Permission::firstOrCreate(['name' => 'guest.sidebar', 'guard_name' => 'web']);
    $guestRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guest', 'guard_name' => 'web']);
    $guestRole->givePermissionTo($guestPerm);

    $guestUser = User::factory()->create();
    $guestUser->assignRole('guest');

    $plainUser = User::factory()->create();

    // Guest user should have guest.sidebar permission via role
    expect($guestUser->can('guest.sidebar'))->toBeTrue();
    expect($plainUser->can('guest.sidebar'))->toBeFalse();
});

it('menolak aduan submission tanpa login di guest submission routes', function () {
    // Guest submission routes require auth (since they are under auth middleware) — prefix guest/
    $this->get('/guest/submissions')
        ->assertRedirect(route('login'));

    $this->get('/guest/submissions/create')
        ->assertRedirect(route('login'));
});
