<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Edit;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Show;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaFormResponse;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

function revisiSubmission(): array
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Revisi',
        'slug' => 'layanan-revisi-'.Str::random(6),
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Revisi',
        'slug' => 'form-revisi-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    $user = User::factory()->create();

    $submission = RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'REV_'.Str::upper(Str::random(8)),
        'status' => SubmissionStatus::MenungguRevisi,
        'files_finalized_at' => now(),
        'items' => [],
    ]);

    RakacaFormResponse::create([
        'rakaca_submission_id' => $submission->id,
        'revise_note' => 'Lampiran KTP kurang jelas, mohon unggah ulang.',
        'requested_revision_at' => now(),
    ]);

    return [$submission, $user];
}

it('menampilkan catatan revisi dan tombol revisi di detail pengajuan', function () {
    [$submission, $user] = revisiSubmission();

    $component = Livewire::actingAs($user)->test(Show::class, ['submission' => $submission]);

    expect($component->html())
        ->toContain('Lampiran KTP kurang jelas, mohon unggah ulang.')
        ->toContain('Revisi Berkas');
});

it('pemohon dapat mengirim ulang tiket menunggu-revisi ke petugas', function () {
    [$submission, $user] = revisiSubmission();

    Livewire::actingAs($user)
        ->test(Edit::class, ['submission' => $submission])
        ->call('submitFiles')
        ->assertHasNoErrors();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::SiapDireview)
        ->and($submission->fresh()->sla_paused_at)->toBeNull();
});
