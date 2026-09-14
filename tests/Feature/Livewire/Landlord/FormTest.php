<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Livewire\Livewire;
use Paparee\Rakaca\Livewire\Pages\Landlord\Form\Form as FormComponent;
use Paparee\Rakaca\Livewire\Pages\Landlord\Form\Index as FormIndex;
use Paparee\Rakaca\Models\Form as FormModel;
use Paparee\Rakaca\Models\RakacaService;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    foreach (['form.read', 'form.create', 'form.update', 'form.delete'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
    foreach (['service.read'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }
});

function formUser(array $perms = ['form.read']): User
{
    $user = User::factory()->create();
    $user->givePermissionTo($perms);

    return $user;
}

it('menolak akses form index tanpa permission', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(FormIndex::class)
        ->assertForbidden();
});

it('menampilkan form index dengan permission', function () {
    $user = formUser(['form.read']);

    Livewire::actingAs($user)
        ->test(FormIndex::class)
        ->assertOk();
});

it('validasi form create wajib service dan name', function () {
    $user = formUser(['form.create', 'form.read']);

    Livewire::actingAs($user)
        ->test(FormComponent::class)
        ->set('name', '')
        ->set('rakaca_service_id', '')
        ->call('save')
        ->assertHasErrors(['name', 'rakaca_service_id']);
});

it('berhasil membuat form dengan field string dan select options', function () {
    $service = RakacaService::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Test Service',
        'slug' => 'test-service',
        'actived' => true,
    ]);

    $user = formUser(['form.create', 'form.read']);

    Livewire::actingAs($user)
        ->test(FormComponent::class)
        ->set('rakaca_service_id', $service->id)
        ->set('name', 'Form Test')
        ->set('slug', 'form-test')
        ->set('actived', true)
        ->set('fields', [
            [
                'key' => 'nama_lengkap',
                'label' => 'Nama Lengkap',
                'type' => 'string',
                'required' => true,
                'placeholder' => 'Masukkan nama',
                'options' => [],
                'order' => 1,
            ],
            [
                'key' => 'kategori',
                'label' => 'Kategori',
                'type' => 'select',
                'required' => true,
                'placeholder' => '',
                'options' => ['Option A', 'Option B', 'Option C'],
                'order' => 2,
            ],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $form = FormModel::where('slug', 'form-test')->first();
    expect($form)->not->toBeNull()
        ->and($form->rakaca_service_id)->toBe($service->id)
        ->and($form->meta['fields'])->toHaveCount(2)
        ->and($form->meta['fields'][1]['options'])->toBe(['Option A', 'Option B', 'Option C']);
});

it('berhasil membuat form dengan 8 tipe field', function () {
    $service = RakacaService::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Service 8',
        'slug' => 'service-8',
        'actived' => true,
    ]);
    $user = formUser(['form.create', 'form.read']);

    $fields = [
        ['key' => 'f_string', 'label' => 'F String', 'type' => 'string', 'required' => true, 'placeholder' => '', 'options' => [], 'order' => 1],
        ['key' => 'f_textarea', 'label' => 'F Textarea', 'type' => 'textarea', 'required' => false, 'placeholder' => '', 'options' => [], 'order' => 2],
        ['key' => 'f_number', 'label' => 'F Number', 'type' => 'number', 'required' => false, 'placeholder' => '', 'options' => [], 'order' => 3],
        ['key' => 'f_email', 'label' => 'F Email', 'type' => 'email', 'required' => false, 'placeholder' => '', 'options' => [], 'order' => 4],
        ['key' => 'f_select', 'label' => 'F Select', 'type' => 'select', 'required' => false, 'placeholder' => '', 'options' => ['A', 'B'], 'order' => 5],
        ['key' => 'f_checkbox', 'label' => 'F Checkbox', 'type' => 'checkbox', 'required' => false, 'placeholder' => '', 'options' => [], 'order' => 6],
        ['key' => 'f_date', 'label' => 'F Date', 'type' => 'date', 'required' => false, 'placeholder' => '', 'options' => [], 'order' => 7],
        ['key' => 'f_file', 'label' => 'F File', 'type' => 'file', 'required' => false, 'placeholder' => '', 'options' => [], 'order' => 8],
    ];

    Livewire::actingAs($user)
        ->test(FormComponent::class)
        ->set('rakaca_service_id', $service->id)
        ->set('name', 'Form 8 Types')
        ->set('slug', 'form-8-types')
        ->set('fields', $fields)
        ->call('save')
        ->assertHasNoErrors();

    $form = FormModel::where('slug', 'form-8-types')->first();
    expect($form->meta['fields'])->toHaveCount(8);
});

it('berhasil mengupdate form', function () {
    $service = RakacaService::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Srv',
        'slug' => 'srv',
        'actived' => true,
    ]);
    $form = FormModel::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Old Form',
        'slug' => 'old-form',
        'meta' => ['fields' => []],
        'actived' => true,
    ]);

    $user = formUser(['form.update', 'form.read']);

    Livewire::actingAs($user)
        ->test(FormComponent::class, ['form' => $form])
        ->set('name', 'Updated Form')
        ->set('slug', 'updated-form')
        ->call('save')
        ->assertHasNoErrors();

    expect($form->fresh()->name)->toBe('Updated Form');
});

it('berhasil menghapus form via delete', function () {
    $service = RakacaService::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'name' => 'Srv Del',
        'slug' => 'srv-del',
        'actived' => true,
    ]);
    $form = FormModel::create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'To Delete',
        'slug' => 'to-delete',
        'meta' => ['fields' => []],
        'actived' => true,
    ]);

    $user = formUser(['form.read', 'form.delete']);

    Livewire::actingAs($user)
        ->test(FormIndex::class)
        ->call('deleteForm', $form->id)
        ->assertDispatched('toast');

    expect(FormModel::where('id', $form->id)->exists())->toBeFalse();
});
