<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Paparee\Rakaca\Models\Form as FormModel;
use Paparee\Rakaca\Models\RakacaService;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('rakaca:install seeds permissions dan sync root', function () {
    $this->artisan('rakaca:install')
        ->assertExitCode(0);

    foreach (['service.read', 'form.read', 'submission.read', 'organization.read', 'bale-list.read', 'bale-user.read', 'analytic.read'] as $perm) {
        expect(Permission::where('name', $perm)->exists())->toBeTrue();
    }

    // Root role should have been synced
    $rootRole = Role::where('name', 'root')->first();
    if ($rootRole) {
        expect($rootRole->permissions()->count())->toBeGreaterThan(0);
    }
});

it('rakaca:make-service membuat service baru via --name', function () {
    $this->artisan('rakaca:make-service', ['--name' => 'Test Service Baru'])
        ->assertExitCode(0);

    expect(RakacaService::where('slug', 'test-service-baru')->exists())->toBeTrue();
    expect(RakacaService::where('slug', 'test-service-baru')->first()->name)->toBe('Test Service Baru');
});

it('rakaca:make-service meminta nama jika --name kosong (interactive)', function () {
    $this->artisan('rakaca:make-service')
        ->expectsQuestion('What is the service name?', 'Interactive Service')
        ->assertExitCode(0);

    expect(RakacaService::where('slug', 'interactive-service')->exists())->toBeTrue();
});

it('rakaca:make-service menolak duplikat slug', function () {
    RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Dup',
        'slug' => 'dup-service',
        'actived' => true,
    ]);

    $this->artisan('rakaca:make-service', ['--name' => 'Dup', '--slug' => 'dup-service'])
        ->assertExitCode(0);

    // Should warn but still succeed (existing service)
    expect(RakacaService::where('slug', 'dup-service')->count())->toBe(1);
});

it('rakaca:make-form membuat form terkait service', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Srv For Form',
        'slug' => 'srv-for-form',
        'actived' => true,
    ]);

    $this->artisan('rakaca:make-form', ['--name' => 'Form Via Command', '--service' => $service->slug])
        ->assertExitCode(0);

    expect(FormModel::where('slug', 'form-via-command')->exists())->toBeTrue();
    expect(FormModel::where('slug', 'form-via-command')->first()->rakaca_service_id)->toBe($service->id);
});

it('rakaca:make-person-service menautkan user ke service (interactive)', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Srv Person',
        'slug' => 'srv-person',
        'actived' => true,
    ]);
    $user = User::factory()->create(['username' => 'personuser']);

    $this->artisan('rakaca:make-person-service')
        ->expectsQuestion('Masukkan username user', 'personuser')
        ->expectsQuestion('Pilih service yang ingin ditautkan:', $service->slug)
        ->assertExitCode(0);

    expect(\Paparee\Rakaca\Models\PersonHasService::where('user_uuid', $user->uuid)->where('rakaca_service_id', $service->id)->exists())->toBeTrue();
});

it('rakaca:make-user-submission membuat submission via form', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Srv Sub',
        'slug' => 'srv-sub',
        'actived' => true,
    ]);
    $form = FormModel::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Sub',
        'slug' => 'form-sub',
        'meta' => ['fields' => []],
        'actived' => true,
    ]);
    $user = User::factory()->create(['username' => 'subuser']);

    $this->artisan('rakaca:make-user-submission', ['--username' => 'subuser', '--form_slug' => 'form-sub'])
        ->assertExitCode(0);

    expect(\Paparee\Rakaca\Models\RakacaSubmission::where('user_uuid', $user->uuid)->where('rakaca_form_id', $form->id)->exists())->toBeTrue();
});
