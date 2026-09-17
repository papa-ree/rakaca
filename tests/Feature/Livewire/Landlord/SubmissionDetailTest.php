<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Detail;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use Spatie\Permission\Models\Permission;

function landlordDetailFixture(): RakacaSubmission
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Service Detail',
        'slug' => 'service-detail-'.Str::random(6),
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Detail',
        'slug' => 'form-detail-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    $user = User::factory()->create();

    return RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'TKT-'.Str::upper(Str::random(8)),
        'status' => SubmissionStatus::SiapDireview,
        'items' => [],
    ]);
}

it('reject mengharuskan rejection_reason minimal 10 karakter', function () {
    foreach (['submission.read', 'submission.update'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $admin = User::factory()->create();
    $admin->givePermissionTo(['submission.read', 'submission.update']);
    $this->actingAs($admin);

    $submission = landlordDetailFixture();

    Livewire::test(Detail::class, ['submission' => $submission])
        ->set('reason', 'pendek')
        ->call('reject')
        ->assertHasErrors(['reason']);

    expect($submission->fresh()->status)->toBe(SubmissionStatus::SiapDireview);
});

it('reject berhasil dengan alasan valid', function () {
    foreach (['submission.read', 'submission.update'] as $perm) {
        Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
    }

    $admin = User::factory()->create();
    $admin->givePermissionTo(['submission.read', 'submission.update']);
    $this->actingAs($admin);

    $submission = landlordDetailFixture();

    Livewire::test(Detail::class, ['submission' => $submission])
        ->set('reason', 'Berkas yang dilampirkan tidak sesuai ketentuan.')
        ->call('reject')
        ->assertHasNoErrors(['reason'])
        ->assertRedirect();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Ditolak);

    $response = $submission->fresh()->response;
    expect($response)->not->toBeNull()
        ->and($response->rejection_reason)->toBe('Berkas yang dilampirkan tidak sesuai ketentuan.');
});
