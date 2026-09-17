<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Illuminate\Support\Str;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaFormResponse;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

it('rakaca:auto-cancel membatalkan tiket menunggu-berkas yang lewat cutoff', function () {
    [$submission] = oldMenungguBerkasSubmission();
    $submission->created_at = now()->subHours(100);
    $submission->save();

    $this->artisan('rakaca:auto-cancel')->assertExitCode(0);

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Dibatalkan);

    $response = RakacaFormResponse::where('rakaca_submission_id', $submission->id)->first();
    expect($response)->not->toBeNull()
        ->and($response->cancelled_reason)->toContain('auto-cancel');
});

it('rakaca:auto-cancel tidak menyentuh tiket muda', function () {
    [$submission] = oldMenungguBerkasSubmission();

    $this->artisan('rakaca:auto-cancel')->assertExitCode(0);

    expect($submission->fresh()->status)->toBe(SubmissionStatus::MenungguBerkas)
        ->and(RakacaFormResponse::where('rakaca_submission_id', $submission->id)->exists())->toBeFalse();
});

it('rakaca:auto-cancel mengabaikan tiket non-menunggu-berkas', function () {
    [$submission] = oldMenungguBerkasSubmission(status: SubmissionStatus::SiapDireview);
    $submission->created_at = now()->subHours(100);
    $submission->save();

    $this->artisan('rakaca:auto-cancel')->assertExitCode(0);

    expect($submission->fresh()->status)->toBe(SubmissionStatus::SiapDireview);
});

function oldMenungguBerkasSubmission(?SubmissionStatus $status = null): array
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Service Auto Cancel',
        'slug' => 'service-auto-cancel-'.Str::random(6),
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Auto Cancel',
        'slug' => 'form-auto-cancel-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    $user = User::factory()->create();

    $submission = RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'TKT-'.Str::upper(Str::random(8)),
        'status' => $status ?? SubmissionStatus::MenungguBerkas,
        'items' => [],
    ]);

    return [$submission, $user];
}
