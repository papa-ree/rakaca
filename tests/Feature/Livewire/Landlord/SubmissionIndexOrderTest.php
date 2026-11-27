<?php

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Index;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use Spatie\Permission\Models\Permission;

it('menampilkan pengajuan terbaru lebih dulu', function () {
    Permission::firstOrCreate(['name' => 'submission.read', 'guard_name' => 'web']);

    $user = User::factory()->create();
    $user->givePermissionTo('submission.read');
    $this->actingAs($user);

    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Urutan',
        'slug' => 'layanan-urutan',
        'sla_days' => 2,
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Urutan',
        'slug' => 'form-urutan',
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    foreach ([
        ['OLD-CODE', now()->subDay(), SubmissionStatus::SiapDireview],
        ['NEW-CODE', now(), SubmissionStatus::SiapDireview],
        ['REVISI-CODE', now(), SubmissionStatus::MenungguRevisi],
        ['BATAL-CODE', now(), SubmissionStatus::Dibatalkan],
    ] as [$code, $createdAt, $status]) {
        $submission = RakacaSubmission::create([
            'user_uuid' => $user->uuid,
            'rakaca_form_id' => $form->id,
            'code' => $code,
            'status' => $status,
            'items' => [],
        ]);

        $submission->created_at = $createdAt;
        $submission->save();
    }

    Livewire::test(Index::class)
        ->assertSeeTextInOrder(['NEW-CODE', 'OLD-CODE'])
        ->assertSeeHtml('dark:bg-amber-900/20')
        ->assertSeeHtml('dark:bg-gray-700');
});
