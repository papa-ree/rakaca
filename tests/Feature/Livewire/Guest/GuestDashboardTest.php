<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Index;
use Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section\ServiceList;
use Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section\StatsStrip;
use Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section\SubmissionStatus as StatusSection;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\PersonHasService;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

it('merender dashboard guest dengan angka, layanan, dan status terakhir', function () {
    $user = User::factory()->create();

    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Dashboard',
        'slug' => 'layanan-dashboard-'.Str::random(6),
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Dashboard',
        'slug' => 'form-dashboard-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    PersonHasService::create([
        'id' => (string) Str::uuid(),
        'user_uuid' => $user->uuid,
        'rakaca_service_id' => $service->id,
        'actived' => true,
    ]);

    RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'TIK-0001',
        'status' => SubmissionStatus::Diproses,
        'items' => [],
    ]);

    Livewire::withoutLazyLoading()->actingAs($user)->test(Index::class)->assertOk();

    Livewire::withoutLazyLoading()->actingAs($user)->test(StatsStrip::class)
        ->assertSee('Layanan Aktif')
        ->assertSee('Pengajuan Menunggu')
        ->assertSee('Pengajuan Selesai');

    Livewire::actingAs($user)->test(ServiceList::class)
        ->assertSee($service->name)
        ->assertSee('service_id=');

    Livewire::withoutLazyLoading()->actingAs($user)->test(StatusSection::class)
        ->assertSee('TIK-0001')
        ->assertSee(SubmissionStatus::Diproses->label())
        ->assertSeeHtml('open: 0')
        ->assertSeeHtml('submission-panel-0');
});

it('memetakan status ke tahapan stepper', function () {
    $section = new StatusSection;

    expect($section->stage(SubmissionStatus::MenungguBerkas))->toBe(0)
        ->and($section->stage(SubmissionStatus::SiapDireview))->toBe(1)
        ->and($section->stage(SubmissionStatus::Diproses))->toBe(2)
        ->and($section->stage(SubmissionStatus::Selesai))->toBe(3)
        ->and($section->badgeClass(SubmissionStatus::Selesai))->toContain('teal');
});
