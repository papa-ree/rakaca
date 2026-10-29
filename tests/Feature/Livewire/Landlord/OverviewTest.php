<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Landlord;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Landlord\Overview\Index as OverviewIndex;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use Spatie\Permission\Models\Permission;

function overviewPermission(string ...$names): void
{
    foreach ($names as $name) {
        Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }
}

function overviewAdmin(array $perms = ['submission.read']): User
{
    overviewPermission('submission.read', ...$perms);

    $user = User::factory()->create();
    $user->givePermissionTo(array_unique([...$perms, 'submission.read']));

    return $user;
}

function overviewService(string $name = 'Layanan A'): RakacaService
{
    return RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => $name,
        'slug' => Str::slug($name).'-'.Str::random(6),
        'actived' => true,
    ]);
}

function overviewForm(RakacaService $service): Form
{
    return Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form '.$service->name,
        'slug' => 'form-'.$service->slug,
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);
}

function overviewSubmission(
    Form $form,
    User $user,
    SubmissionStatus|string $status,
    ?string $createdAt = null,
): RakacaSubmission {
    $submission = RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'OVW-'.Str::upper(Str::random(10)),
        'status' => $status,
        'items' => ['data' => []],
    ]);

    if ($createdAt !== null) {
        $submission->timestamps = false;
        $submission->created_at = $createdAt;
        $submission->saveQuietly();
    }

    return $submission;
}

it('menolak akses overview tanpa permission submission.read', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(OverviewIndex::class)
        ->assertForbidden();
});

it('menghitung statistik hanya untuk pengajuan yang belum selesai', function () {
    $admin = overviewAdmin();
    $form = overviewForm(overviewService());
    $applicant = User::factory()->create();

    overviewSubmission($form, $applicant, SubmissionStatus::MenungguBerkas);
    overviewSubmission($form, $applicant, SubmissionStatus::MenungguBerkas);
    overviewSubmission($form, $applicant, SubmissionStatus::Diproses);
    overviewSubmission($form, $applicant, SubmissionStatus::Selesai);
    overviewSubmission($form, $applicant, SubmissionStatus::Ditolak);

    $stats = Livewire::actingAs($admin)->test(OverviewIndex::class)->viewData('stats');

    expect(collect($stats)->pluck('status')->all())->toBe([
        SubmissionStatus::MenungguBerkas,
        SubmissionStatus::SiapDireview,
        SubmissionStatus::Diproses,
        SubmissionStatus::MenungguRevisi,
    ])->and(collect($stats)->pluck('total')->all())->toBe([2, 0, 1, 0]);
});

it('memetakan status lama ke status kanonik pada statistik', function () {
    $admin = overviewAdmin();
    $form = overviewForm(overviewService());
    $applicant = User::factory()->create();

    // Status lama ditulis langsung: cast model hanya menerima value enum kanonik.
    foreach (['pending', 'review'] as $legacy) {
        DB::table('rakaca_submissions')->insert([
            'id' => (string) Str::uuid(),
            'user_uuid' => $applicant->uuid,
            'rakaca_form_id' => $form->id,
            'code' => 'LEG-'.Str::upper(Str::random(8)),
            'status' => $legacy,
            'items' => '{}',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $stats = Livewire::actingAs($admin)->test(OverviewIndex::class)->viewData('stats');

    expect($stats[0]['total'])->toBe(1)
        ->and($stats[1]['total'])->toBe(1);
});

it('mengelompokkan pengajuan per layanan sepanjang waktu', function () {
    $admin = overviewAdmin();
    $serviceA = overviewService('Layanan A');
    $serviceB = overviewService('Layanan B');
    $formA = overviewForm($serviceA);
    $formB = overviewForm($serviceB);
    $applicant = User::factory()->create();

    overviewSubmission($formA, $applicant, SubmissionStatus::Selesai);
    overviewSubmission($formA, $applicant, SubmissionStatus::Diproses);
    overviewSubmission($formB, $applicant, SubmissionStatus::MenungguBerkas);

    $services = Livewire::actingAs($admin)->test(OverviewIndex::class)->viewData('services');

    expect($services->pluck('total', 'service')->all())->toBe([
        'Layanan A' => 2,
        'Layanan B' => 1,
    ]);
});

it('menghitung pengajuan per bulan dalam rentang terpilih', function () {
    $admin = overviewAdmin();
    $form = overviewForm(overviewService());
    $applicant = User::factory()->create();

    overviewSubmission($form, $applicant, SubmissionStatus::Selesai, '2026-01-05 10:00:00');
    overviewSubmission($form, $applicant, SubmissionStatus::Selesai, '2026-01-20 10:00:00');
    overviewSubmission($form, $applicant, SubmissionStatus::Selesai, '2026-02-02 10:00:00');

    $component = Livewire::withQueryParams([
        'dari' => '2026-01-01',
        'sampai' => '2026-01-31',
    ])->actingAs($admin)->test(OverviewIndex::class);

    expect($component->viewData('monthly'))->toBe(['2026-01' => 2]);
});

it('memotong rentang lebih dari 90 hari lewat query string', function () {
    $admin = overviewAdmin();

    $component = Livewire::withQueryParams([
        'dari' => '2026-01-01',
        'sampai' => '2026-06-01',
    ])->actingAs($admin)->test(OverviewIndex::class);

    expect($component->get('dari'))->toBe('2026-01-01')
        ->and($component->get('sampai'))->toBe('2026-03-31')
        ->and($component->get('periodError'))->not->toBeNull();
});

it('mengubah rentang lewat preset dan input tanggal', function () {
    $admin = overviewAdmin();

    $component = Livewire::actingAs($admin)->test(OverviewIndex::class)
        ->call('setPreset', 7);

    expect($component->instance()->dari)->toBe(now()->subDays(6)->toDateString())
        ->and($component->instance()->sampai)->toBe(now()->toDateString());

    $component->set('dari', '2026-02-01');

    // Tanggal akhir yang tersimpan ikut dipotong ke batas 90 hari.
    expect($component->instance()->sampai)->toBe('2026-05-01');
});

it('menampilkan pengajuan terbaru maksimal delapan baris', function () {
    $admin = overviewAdmin();
    $form = overviewForm(overviewService());
    $applicant = User::factory()->create();

    for ($i = 0; $i < 10; $i++) {
        overviewSubmission($form, $applicant, SubmissionStatus::MenungguBerkas, now()->subMinutes($i)->toDateTimeString());
    }

    $recent = Livewire::actingAs($admin)->test(OverviewIndex::class)->viewData('recent');

    expect($recent)->toHaveCount(8);
});

it('menampilkan halaman overview untuk pemilik permission', function () {
    $admin = overviewAdmin();

    Livewire::actingAs($admin)
        ->test(OverviewIndex::class)
        ->assertOk()
        ->assertSee('Overview Pengajuan');
});
