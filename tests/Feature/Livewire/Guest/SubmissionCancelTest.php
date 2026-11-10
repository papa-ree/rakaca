<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Index;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

it('membatalkan pengajuan dari daftar pengajuan', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Batal',
        'slug' => 'layanan-batal-'.Str::random(6),
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Batal',
        'slug' => 'form-batal-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    $user = User::factory()->create();

    $submission = RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'SUB_'.Str::upper(Str::random(8)),
        'status' => SubmissionStatus::SiapDireview,
        'files_finalized_at' => now(),
        'items' => [],
    ]);

    $component = Livewire::actingAs($user)->test(Index::class);

    // Baris tabel dirender di dalam data-table, jadi tombol batal harus
    // mengirim event ke Index, bukan memanggil method di komponen tabel.
    expect($component->html())->toContain("\$dispatch('cancelSubmission'");

    $component->call('cancelSubmission', $submission->id)
        ->assertDispatched('toast');

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Dibatalkan);
});
