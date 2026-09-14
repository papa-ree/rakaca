<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Form as BaleUserForm;
use Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Index as BaleUserIndex;
use Paparee\Rakaca\Models\BaleList;
use Paparee\Rakaca\Models\BaleUser;
use Paparee\Rakaca\Models\Organization;
use Paparee\Rakaca\Models\PersonHasService;
use Paparee\Rakaca\Models\RakacaService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    foreach (['bale-user.read', 'bale-user.create', 'bale-user.update', 'bale-user.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
    // Service needed for user filtering
    RakacaService::firstOrCreate(['slug' => 'bale-cms'], [
        'id' => (string) Str::uuid(),
        'name' => 'Bale CMS',
        'actived' => true,
    ]);
});

function baleUserAdmin(array $perms = ['bale-user.read']): User
{
    $user = User::factory()->create();
    $user->givePermissionTo($perms);

    return $user;
}

function createBaleWithOrg(): BaleList
{
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Org '.Str::random(4),
        'slug' => 'org-'.Str::random(4),
        'created_by' => User::factory()->create()->uuid,
    ]);

    return BaleList::create([
        'id' => (string) Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale '.Str::random(4),
        'slug' => 'bale-'.Str::random(4),
        'database_host' => 'localhost',
        'database_name' => 'test_db_'.Str::random(6),
        'database_username' => 'u',
        'database_password' => 'p',
        'is_active' => true,
    ]);
}

function userWithBaleCmsService(): User
{
    $user = User::factory()->create();
    $service = RakacaService::where('slug', 'bale-cms')->first();
    PersonHasService::create([
        'id' => (string) Str::uuid(),
        'user_uuid' => $user->uuid,
        'rakaca_service_id' => $service->id,
        'actived' => true,
    ]);

    return $user;
}

it('menampilkan index bale-user dengan permission', function () {
    $admin = baleUserAdmin(['bale-user.read']);

    Livewire::actingAs($admin)->test(BaleUserIndex::class)->assertOk();
});

it('menolak index tanpa permission', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)->test(BaleUserIndex::class)->assertForbidden();
});

it('berhasil membuat bale-user assignment untuk user dengan bale-cms service', function () {
    $bale = createBaleWithOrg();
    $targetUser = userWithBaleCmsService();
    $admin = baleUserAdmin(['bale-user.create', 'bale-user.read']);

    Livewire::actingAs($admin)
        ->test(BaleUserForm::class)
        ->set('bale_id', $bale->id)
        ->set('user_uuid', $targetUser->uuid)
        ->set('role', 'user')
        ->call('save')
        ->assertHasNoErrors();

    expect(BaleUser::where('bale_id', $bale->id)->where('user_uuid', $targetUser->uuid)->exists())->toBeTrue();
});

it('validasi gagal jika user tidak memiliki bale-cms service dan bukan god role', function () {
    $bale = createBaleWithOrg();
    $plainUser = User::factory()->create(); // no service, no god role
    $admin = baleUserAdmin(['bale-user.create']);

    Livewire::actingAs($admin)
        ->test(BaleUserForm::class)
        ->set('bale_id', $bale->id)
        ->set('user_uuid', $plainUser->uuid)
        ->set('role', 'user')
        ->call('save')
        ->assertHasErrors(['user_uuid']);
});

it('mencegah duplikat assignment user ke bale yang sama', function () {
    $bale = createBaleWithOrg();
    $targetUser = userWithBaleCmsService();
    BaleUser::create([
        'id' => (string) Str::uuid(),
        'bale_id' => $bale->id,
        'user_uuid' => $targetUser->uuid,
        'role' => 'user',
    ]);
    $admin = baleUserAdmin(['bale-user.create']);

    Livewire::actingAs($admin)
        ->test(BaleUserForm::class)
        ->set('bale_id', $bale->id)
        ->set('user_uuid', $targetUser->uuid)
        ->set('role', 'user')
        ->call('save')
        ->assertHasErrors(['user_uuid']);
});

it('berhasil mengupdate role bale-user', function () {
    $bale = createBaleWithOrg();
    $bale2 = createBaleWithOrg();
    $targetUser = userWithBaleCmsService();
    $assignment = BaleUser::create([
        'id' => (string) Str::uuid(),
        'bale_id' => $bale->id,
        'user_uuid' => $targetUser->uuid,
        'role' => 'user',
    ]);
    $admin = baleUserAdmin(['bale-user.update', 'bale-user.read']);

    Livewire::actingAs($admin)
        ->test(BaleUserForm::class, ['baleUser' => $assignment->id])
        ->set('bale_id', $bale2->id)
        ->set('role', 'admin')
        ->call('save')
        ->assertHasNoErrors();

    expect($assignment->fresh()->role)->toBe('admin')
        ->and($assignment->fresh()->bale_id)->toBe($bale2->id);
});

it('mencegah edit assignment root oleh non-root', function () {
    // Create root role and user
    $rootRole = Role::firstOrCreate(['name' => 'root', 'guard_name' => 'web']);
    $rootUser = User::factory()->create();
    $rootUser->assignRole('root');
    // Ensure bale-cms service for rootUser to be assignable
    $service = RakacaService::where('slug', 'bale-cms')->first();
    PersonHasService::firstOrCreate(
        ['user_uuid' => $rootUser->uuid, 'rakaca_service_id' => $service->id],
        ['id' => (string) Str::uuid(), 'actived' => true]
    );
    $bale = createBaleWithOrg();
    $assignment = BaleUser::create([
        'id' => (string) Str::uuid(),
        'bale_id' => $bale->id,
        'user_uuid' => $rootUser->uuid,
        'role' => 'root',
    ]);

    $admin = baleUserAdmin(['bale-user.update', 'bale-user.read']);
    // admin is not root
    Livewire::actingAs($admin)
        ->test(BaleUserForm::class, ['baleUser' => $assignment->id])
        ->assertForbidden();
});

it('berhasil menghapus bale-user', function () {
    $bale = createBaleWithOrg();
    $targetUser = userWithBaleCmsService();
    $assignment = BaleUser::create([
        'id' => (string) Str::uuid(),
        'bale_id' => $bale->id,
        'user_uuid' => $targetUser->uuid,
        'role' => 'user',
    ]);
    $admin = baleUserAdmin(['bale-user.read', 'bale-user.delete']);

    // Index component has delete via Table? Use BaleUser Section Table component
    // For simplicity, test direct delete via model (permission already tested)
    // Here test Index delete via Table component if needed, but we test via direct assignment delete
    // Use the Table component's delete method
    $tableClass = \Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Section\Table::class;
    Livewire::actingAs($admin)
        ->test($tableClass)
        ->call('deleteBaleUser', $assignment->id)
        ->assertDispatched('toast');

    expect(BaleUser::where('id', $assignment->id)->exists())->toBeFalse();
});
