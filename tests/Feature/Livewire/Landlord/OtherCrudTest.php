<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Livewire\Pages\Landlord\Analytic\Create as AnalyticCreate;
use Paparee\Rakaca\Livewire\Pages\Landlord\Analytic\Edit;
use Paparee\Rakaca\Livewire\Pages\Landlord\Analytic\Index as AnalyticIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\BaleList\Create as BaleListCreate;
use Paparee\Rakaca\Livewire\Pages\Landlord\BaleList\Index as BaleListIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\Organization\Create as OrgCreate;
use Paparee\Rakaca\Livewire\Pages\Landlord\Organization\Index as OrgIndex;
use Paparee\Rakaca\Models\BaleList;
use Paparee\Rakaca\Models\Organization;
use Paparee\Rakaca\Models\TenantAnalytics;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    foreach ([
        'organization.read', 'organization.create', 'organization.update', 'organization.delete',
        'bale-list.read', 'bale-list.create', 'bale-list.update', 'bale-list.delete',
        'analytic.read', 'analytic.create', 'analytic.update', 'analytic.delete',
    ] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
});

function otherUser(array $perms): User
{
    $user = User::factory()->create();
    $user->givePermissionTo($perms);

    return $user;
}

// Organization
it('menampilkan index organization dengan permission', function () {
    $user = otherUser(['organization.read']);

    Livewire::actingAs($user)->test(OrgIndex::class)->assertOk();
});

it('berhasil membuat organization', function () {
    $user = otherUser(['organization.create']);

    Livewire::actingAs($user)
        ->test(OrgCreate::class)
        ->set('name', 'Test Org')
        ->set('slug', 'test-org')
        ->call('save')
        ->assertHasNoErrors();

    expect(Organization::where('slug', 'test-org')->exists())->toBeTrue();
});

it('validasi organization wajib name', function () {
    $user = otherUser(['organization.create']);

    Livewire::actingAs($user)
        ->test(OrgCreate::class)
        ->set('name', '')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['name']);
});

it('berhasil menghapus organization', function () {
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Delete Me',
        'slug' => 'delete-me',
        'created_by' => User::factory()->create()->uuid,
    ]);
    $user = otherUser(['organization.read', 'organization.delete']);

    Livewire::actingAs($user)
        ->test(OrgIndex::class)
        ->call('deleteOrganization', $org->id)
        ->assertDispatched('toast');

    expect(Organization::where('id', $org->id)->exists())->toBeFalse();
});

// BaleList
it('menampilkan index bale-list dengan permission', function () {
    $user = otherUser(['bale-list.read']);

    Livewire::actingAs($user)->test(BaleListIndex::class)->assertOk();
});

it('berhasil membuat bale-list', function () {
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Org For Bale',
        'slug' => 'org-for-bale',
        'created_by' => User::factory()->create()->uuid,
    ]);
    $user = otherUser(['bale-list.create']);

    Livewire::actingAs($user)
        ->test(BaleListCreate::class)
        ->set('organization_id', $org->id)
        ->set('name', 'Bale Test')
        ->set('slug', 'bale-test')
        ->set('database_host', '127.0.0.1')
        ->set('database_name', 'test_db')
        ->set('database_username', 'tester')
        ->set('database_password', 'secret123')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(BaleList::where('slug', 'bale-test')->exists())->toBeTrue();
    $bale = BaleList::where('slug', 'bale-test')->first();
    // encrypted cast should decrypt correctly
    expect($bale->database_username)->toBe('tester');
});

it('berhasil menghapus bale-list', function () {
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Org Del',
        'slug' => 'org-del',
        'created_by' => User::factory()->create()->uuid,
    ]);
    $bale = BaleList::create([
        'id' => (string) Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Del',
        'slug' => 'bale-del',
        'database_host' => 'localhost',
        'database_name' => 'del_db',
        'database_username' => 'u',
        'database_password' => 'p',
        'is_active' => true,
    ]);
    $user = otherUser(['bale-list.read', 'bale-list.delete']);

    Livewire::actingAs($user)
        ->test(BaleListIndex::class)
        ->call('deleteBaleList', $bale->id)
        ->assertDispatched('toast');

    expect(BaleList::where('id', $bale->id)->exists())->toBeFalse();
});

// Analytic
it('menampilkan index analytic dengan permission', function () {
    $user = otherUser(['analytic.read']);

    Livewire::actingAs($user)->test(AnalyticIndex::class)->assertOk();
});

it('berhasil membuat analytic dengan enabled true', function () {
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Org Analytic',
        'slug' => 'org-analytic',
        'created_by' => User::factory()->create()->uuid,
    ]);
    $bale = BaleList::create([
        'id' => (string) Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Analytic',
        'slug' => 'bale-analytic',
        'database_host' => 'localhost',
        'database_name' => 'analytic_db',
        'database_username' => 'u',
        'database_password' => 'p',
        'is_active' => true,
    ]);
    $user = otherUser(['analytic.create']);

    Livewire::actingAs($user)
        ->test(AnalyticCreate::class)
        ->set('bale_id', $bale->id)
        ->set('provider', 'umami')
        ->set('website_id', (string) Str::uuid())
        ->set('domain', 'example.com')
        ->set('enabled', true)
        ->call('save')
        ->assertHasNoErrors();

    expect(TenantAnalytics::where('bale_id', $bale->id)->exists())->toBeTrue();
});

it('berhasil membuat analytic dengan enabled false (toggle)', function () {
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Org Analytic 2',
        'slug' => 'org-analytic-2',
        'created_by' => User::factory()->create()->uuid,
    ]);
    $bale = BaleList::create([
        'id' => (string) Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Analytic 2',
        'slug' => 'bale-analytic-2',
        'database_host' => 'localhost',
        'database_name' => 'analytic2_db',
        'database_username' => 'u',
        'database_password' => 'p',
        'is_active' => true,
    ]);
    $user = otherUser(['analytic.create']);

    Livewire::actingAs($user)
        ->test(AnalyticCreate::class)
        ->set('bale_id', $bale->id)
        ->set('provider', 'umami')
        ->set('website_id', (string) Str::uuid())
        ->set('domain', 'example2.com')
        ->set('enabled', false)
        ->call('save')
        ->assertHasNoErrors();

    $analytic = TenantAnalytics::where('bale_id', $bale->id)->first();
    expect($analytic->enabled)->toBeFalse();
});

it('analytic edit toggle enabled dapat diupdate', function () {
    $org = Organization::create([
        'id' => (string) Str::uuid(),
        'name' => 'Org Edit',
        'slug' => 'org-edit',
        'created_by' => User::factory()->create()->uuid,
    ]);
    $bale = BaleList::create([
        'id' => (string) Str::uuid(),
        'organization_id' => $org->id,
        'name' => 'Bale Edit',
        'slug' => 'bale-edit',
        'database_host' => 'localhost',
        'database_name' => 'edit_db',
        'database_username' => 'u',
        'database_password' => 'p',
        'is_active' => true,
    ]);
    $analytic = TenantAnalytics::create([
        'id' => (string) Str::uuid(),
        'bale_id' => $bale->id,
        'provider' => 'umami',
        'website_id' => (string) Str::uuid(),
        'domain' => 'edit.com',
        'enabled' => true,
    ]);
    // Give update permission and test edit component
    $user = otherUser(['analytic.update', 'analytic.read']);
    $editClass = Edit::class;

    Livewire::actingAs($user)
        ->test($editClass, ['analytic' => $analytic->id])
        ->set('enabled', false)
        ->call('save')
        ->assertHasNoErrors();

    expect($analytic->fresh()->enabled)->toBeFalse();
});
