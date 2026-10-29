<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Create as GuestSubmissionCreate;
use Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Create as LandlordSubmissionCreate;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use Spatie\Permission\Models\Permission;

function manualSubmissionPermission(string ...$names): void
{
    foreach ($names as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }
}

function manualSubmissionForm(): Form
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Manual',
        'slug' => 'layanan-manual-'.Str::random(6),
        'actived' => true,
    ]);

    return Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Manual',
        'slug' => 'form-manual-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);
}

it('menolak halaman tambah pengajuan tanpa permission submission.create', function () {
    manualSubmissionPermission('submission.create');

    Livewire::actingAs(User::factory()->create())
        ->test(LandlordSubmissionCreate::class)
        ->assertForbidden();
});

it('mewajibkan pemohon yang terdaftar', function () {
    manualSubmissionPermission('submission.create', 'submission.read');

    $admin = User::factory()->create();
    $admin->givePermissionTo(['submission.create', 'submission.read']);

    Livewire::actingAs($admin)
        ->test(LandlordSubmissionCreate::class)
        ->set('form_id', manualSubmissionForm()->id)
        ->set('user_uuid', (string) Str::uuid())
        ->call('save')
        ->assertHasErrors(['user_uuid']);

    expect(RakacaSubmission::count())->toBe(0);
});

it('membuat pengajuan atas nama pemohon dan mengarahkan ke detail', function () {
    manualSubmissionPermission('submission.create', 'submission.read');

    $admin = User::factory()->create();
    $admin->givePermissionTo(['submission.create', 'submission.read']);

    $applicant = User::factory()->create();
    $form = manualSubmissionForm();

    $component = Livewire::actingAs($admin)
        ->test(LandlordSubmissionCreate::class)
        ->set('form_id', $form->id)
        ->set('user_uuid', $applicant->uuid)
        ->call('save')
        ->assertHasNoErrors();

    $submission = RakacaSubmission::firstOrFail();

    expect($submission->user_uuid)->toBe($applicant->uuid)
        ->and($submission->status)->toBe(SubmissionStatus::MenungguBerkas)
        ->and($submission->user_uuid)->not->toBe($admin->uuid);

    $component->assertRedirect(
        route('rakaca.landlord.submission.detail', $submission->id)
    );
});

it('pengajuan guest tetap memakai user yang login', function () {
    $user = User::factory()->create();
    $form = manualSubmissionForm();

    Livewire::actingAs($user)
        ->test(GuestSubmissionCreate::class)
        ->set('form_id', $form->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('rakaca.guest-dashboard.index'));

    $submission = RakacaSubmission::firstOrFail();

    expect($submission->user_uuid)->toBe($user->uuid)
        ->and($submission->status)->toBe(SubmissionStatus::MenungguBerkas);
});
