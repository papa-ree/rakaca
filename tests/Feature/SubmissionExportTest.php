<?php

use App\Models\User;
use Illuminate\Support\Str;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Permission;

function exportUser(bool $withExport = true): User
{
    $permissions = $withExport ? ['dashboard', 'submission.export'] : ['dashboard'];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
    }

    $user = User::factory()->create();
    $user->givePermissionTo($permissions);

    return $user;
}

function exportSubmission(): RakacaSubmission
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Ekspor',
        'slug' => 'layanan-ekspor',
        'sla_days' => 2,
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Ekspor',
        'slug' => 'form-ekspor',
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    return RakacaSubmission::create([
        'user_uuid' => User::factory()->create()->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'EXP-'.Str::upper(Str::random(8)),
        'status' => SubmissionStatus::SiapDireview,
        'items' => [],
    ]);
}

it('menolak ekspor tanpa permission submission.export', function () {
    $this->actingAs(exportUser(false))
        ->get(route('rakaca.landlord.submission.export', 'csv'))
        ->assertForbidden();
});

it('mengunduh CSV semua pengajuan dengan BOM dan header', function () {
    $submission = exportSubmission();

    $response = $this->actingAs(exportUser())
        ->get(route('rakaca.landlord.submission.export', 'csv'));

    $response->assertOk();

    $content = $response->getContent();

    expect($response->headers->get('content-type'))->toContain('text/csv')
        ->and(str_starts_with($content, "\xEF\xBB\xBF"))->toBeTrue()
        ->and($content)->toContain('Kode')
        ->and($content)->toContain($submission->code);
});

it('mengunduh XLSX yang bisa dibaca kembali', function () {
    $submission = exportSubmission();

    $response = $this->actingAs(exportUser())
        ->get(route('rakaca.landlord.submission.export', 'xlsx'));

    $response->assertOk();

    $path = tempnam(sys_get_temp_dir(), 'rakaca-xlsx');
    file_put_contents($path, $response->getContent());

    $sheet = IOFactory::load($path)->getActiveSheet();

    expect($sheet->getCell('A1')->getValue())->toBe('Kode')
        ->and($sheet->getCell('A2')->getValue())->toBe($submission->code);

    @unlink($path);
});
