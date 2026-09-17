<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaFormResponse;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Services\TicketWorkflowService;
use Symfony\Component\HttpKernel\Exception\HttpException;

function rakacaSubmission(): RakacaSubmission
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Service Testing',
        'slug' => 'service-testing',
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Testing',
        'slug' => 'form-testing',
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    $user = User::factory()->create();

    return RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'TKT-'.Str::upper(Str::random(8)),
        'status' => SubmissionStatus::MenungguBerkas,
        'items' => [],
    ]);
}

it('submitFiles memfinalisasi berkas dan pindah ke siap-direview', function () {
    $submission = rakacaSubmission();

    app(TicketWorkflowService::class, ['submission' => $submission])->submitFiles();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::SiapDireview)
        ->and($submission->fresh()->files_finalized_at)->not->toBeNull();
});

it('takeOver membuat response dan pindah ke diproses', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::SiapDireview]);

    $user = User::factory()->create();
    $this->actingAs($user);

    app(TicketWorkflowService::class, ['submission' => $submission])->takeOver();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Diproses);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response)->not->toBeNull()
        ->and($response->processed_by)->toBe($user->uuid)
        ->and($response->processed_at)->not->toBeNull();
});

it('reject menolak tiket dengan alasan', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::SiapDireview]);

    $user = User::factory()->create();
    $this->actingAs($user);

    app(TicketWorkflowService::class, ['submission' => $submission])->reject('Alasan penolakan jelas');

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Ditolak);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response->rejection_reason)->toBe('Alasan penolakan jelas')
        ->and($response->rejected_at)->not->toBeNull();
});

it('requestRevision meminta revisi saat diproses', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::Diproses]);

    app(TicketWorkflowService::class, ['submission' => $submission])->requestRevision('Lengkapi dokumen KTP.');

    expect($submission->fresh()->status)->toBe(SubmissionStatus::MenungguRevisi);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response->revise_note)->toBe('Lengkapi dokumen KTP.')
        ->and($response->requested_revision_at)->not->toBeNull();
});

it('complete menyelesaikan tiket dengan data resolusi', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::Diproses]);

    $user = User::factory()->create();
    $this->actingAs($user);

    app(TicketWorkflowService::class, ['submission' => $submission])->complete([
        'disetujui' => true,
        'catatan' => 'Dokumen diterima',
    ]);

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Selesai);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response->resolution_data)->toBe([
        'disetujui' => true,
        'catatan' => 'Dokumen diterima',
    ])->and($response->resolved_at)->not->toBeNull()
        ->and($response->resolved_by)->toBe($user->uuid);
});

it('userCancel hanya oleh pemilik tiket', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::MenungguBerkas]);

    $owner = User::where('uuid', $submission->user_uuid)->first();
    $this->actingAs($owner);

    app(TicketWorkflowService::class, ['submission' => $submission])->userCancel();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Dibatalkan);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response->cancelled_reason)->toBe('user');
});

it('userCancel ditolak untuk non-owner', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::MenungguBerkas]);

    $stranger = User::factory()->create();
    $this->actingAs($stranger);

    try {
        app(TicketWorkflowService::class, ['submission' => $submission])->userCancel();
        $this->fail('Harusnya abort 403.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }

    expect($submission->fresh()->status)->toBe(SubmissionStatus::MenungguBerkas);
});

it('autoCancel membatalkan tiket menunggu berkas', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::MenungguBerkas]);

    app(TicketWorkflowService::class, ['submission' => $submission])->autoCancel();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Dibatalkan);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response->cancelled_reason)->toContain('auto-cancel');
});

it('memblokir transisi ilegal dengan 403', function () {
    $submission = rakacaSubmission();
    $submission->update(['status' => SubmissionStatus::MenungguBerkas]);

    // MenungguBerkas -> Selesai tidak diizinkan
    try {
        app(TicketWorkflowService::class, ['submission' => $submission])->complete(['x' => 1]);
        $this->fail('Harusnya abort 403.');
    } catch (HttpException $e) {
        expect($e->getStatusCode())->toBe(403);
    }

    expect($submission->fresh()->status)->toBe(SubmissionStatus::MenungguBerkas);
});
