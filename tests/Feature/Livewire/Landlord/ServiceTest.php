<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Livewire\Pages\Landlord\Service\Form as ServiceForm;
use Paparee\Rakaca\Livewire\Pages\Landlord\Service\Index as ServiceIndex;
use Paparee\Rakaca\Models\RakacaService;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    // Seed permissions needed for service CRUD
    foreach (['service.read', 'service.create', 'service.update', 'service.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
});

function serviceUser(array $perms = ['service.read']): User
{
    $user = User::factory()->create();
    $user->givePermissionTo($perms);

    return $user;
}

it('menolak akses index tanpa permission service.read', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ServiceIndex::class)
        ->assertForbidden();
});

it('menampilkan index service ketika memiliki permission', function () {
    $user = serviceUser(['service.read']);

    Livewire::actingAs($user)
        ->test(ServiceIndex::class)
        ->assertOk()
        ->assertSee('Service Management');
});

it('validasi form create service wajib name', function () {
    $user = serviceUser(['service.create', 'service.read']);

    Livewire::actingAs($user)
        ->test(ServiceForm::class)
        ->set('name', '')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('berhasil membuat service baru', function () {
    $user = serviceUser(['service.create', 'service.read']);

    Livewire::actingAs($user)
        ->test(ServiceForm::class)
        ->set('name', 'Bale CMS')
        ->set('slug', 'bale-cms')
        ->set('icon', 'layers')
        ->set('description', 'Layanan CMS Bale')
        ->set('actived', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(RakacaService::where('slug', 'bale-cms')->exists())->toBeTrue();

    $service = RakacaService::where('slug', 'bale-cms')->first();
    expect($service->name)->toBe('Bale CMS')
        ->and($service->actived)->toBeTrue();
});

it('menolak duplikat slug saat create', function () {
    RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Existing',
        'slug' => 'duplicate-slug',
        'actived' => true,
    ]);

    $user = serviceUser(['service.create', 'service.read']);

    Livewire::actingAs($user)
        ->test(ServiceForm::class)
        ->set('name', 'Another')
        ->set('slug', 'duplicate-slug')
        ->call('save')
        ->assertHasErrors(['slug']);
});

it('berhasil mengupdate service', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Old Name',
        'slug' => 'old-name',
        'actived' => true,
    ]);

    $user = serviceUser(['service.update', 'service.read']);

    Livewire::actingAs($user)
        ->test(ServiceForm::class, ['service' => $service])
        ->set('name', 'New Name')
        ->set('slug', 'new-name')
        ->set('description', 'Updated desc')
        ->call('save')
        ->assertHasNoErrors();

    expect($service->fresh()->name)->toBe('New Name')
        ->and($service->fresh()->slug)->toBe('new-name');
});

it('berhasil menghapus service via deleteItem', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'To Delete',
        'slug' => 'to-delete',
        'actived' => true,
    ]);

    $user = serviceUser(['service.read', 'service.delete']);

    Livewire::actingAs($user)
        ->test(ServiceIndex::class)
        ->call('deleteService', $service->id)
        ->assertDispatched('toast');

    expect(RakacaService::where('id', $service->id)->exists())->toBeFalse();
});

it('menolak hapus tanpa permission delete', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Protected',
        'slug' => 'protected',
        'actived' => true,
    ]);

    $user = serviceUser(['service.read']);

    Livewire::actingAs($user)
        ->test(ServiceIndex::class)
        ->call('deleteService', $service->id)
        ->assertForbidden();

    expect(RakacaService::where('id', $service->id)->exists())->toBeTrue();
});
