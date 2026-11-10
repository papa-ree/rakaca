<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Create;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Edit;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

function srikandiForm(array $fields = []): Form
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Srikandi',
        'slug' => 'layanan-srikandi-'.Str::random(6),
        'actived' => true,
    ]);

    return Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Srikandi',
        'slug' => 'form-srikandi-'.Str::random(6),
        'meta' => ['fields' => $fields],
        'response_form_schema' => [],
        'actived' => true,
    ]);
}

function srikandiNaskah(string $nomor, string $tanggal, array $overrides = []): string
{
    $id = (string) Str::uuid();

    DB::table('srikandi_naskah')->insert([
        'id' => $id,
        'sumber' => 'rak-srikandi',
        'nomor_naskah' => $nomor,
        'tanggal_naskah' => $tanggal,
        'hal' => 'Hal '.$tanggal,
        'ringkasan' => 'Ringkasan '.$tanggal,
        'pengirim' => 'Pengirim '.$tanggal,
        'first_seen_at' => now(),
        'last_seen_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
        ...$overrides,
    ]);

    return $id;
}

beforeEach(function () {
    if (Schema::hasTable('srikandi_naskah')) {
        return;
    }

    Schema::create('srikandi_naskah', function ($table) {
        $table->uuid('id')->primary();
        $table->string('sumber', 64);
        $table->string('nomor_naskah', 191);
        $table->unsignedSmallInteger('tahun')->default(0);
        $table->date('tanggal_naskah')->nullable();
        $table->text('hal')->nullable();
        $table->text('ringkasan')->nullable();
        $table->string('pengirim', 191)->nullable();
        $table->timestamp('first_seen_at');
        $table->timestamp('last_seen_at');
        $table->timestamps();
    });
});

it('menemukan surat hanya dengan nomor naskah yang persis', function () {
    srikandiNaskah('400.6/KH/5/BUDAYA/2026', '2026-05-10');

    Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('source', 'srikandi')
        ->set('nomor_naskah', '400.6/KH/5')
        ->call('cariSurat')
        ->assertSet('suratResults', [])
        ->assertSet('suratError', __('Surat tidak ditemukan. Pastikan nomor naskah lengkap, atau coba beberapa saat lagi.'));

    Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('source', 'srikandi')
        ->set('nomor_naskah', '400.6/KH/5/BUDAYA/2026')
        ->call('cariSurat')
        ->assertCount('suratResults', 1);
});

it('membatasi hasil pencarian ke tiga surat terbaru', function () {
    // Nomor sama bisa muncul lebih dari sekali (surat diperbarui), jadi limit 3 berlaku.
    srikandiNaskah('400.1/KH/1/2026', '2023-01-01', ['tahun' => 2023]);
    srikandiNaskah('400.1/KH/1/2026', '2024-01-01', ['tahun' => 2024]);
    srikandiNaskah('400.1/KH/1/2026', '2025-01-01', ['tahun' => 2025]);
    srikandiNaskah('400.1/KH/1/2026', '2026-01-01', ['tahun' => 2026]);

    $component = Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('source', 'srikandi')
        ->set('nomor_naskah', '400.1/KH/1/2026')
        ->call('cariSurat');

    $results = $component->get('suratResults');

    expect($results)->toHaveCount(3)
        ->and($results[0]['tanggal_naskah'])->toStartWith('2026-01-01')
        ->and($results[1]['tanggal_naskah'])->toStartWith('2025-01-01');
});

it('menyimpan salinan surat dan langsung siap direview tanpa berkas', function () {
    Storage::fake('public');
    Storage::fake('local');

    $form = srikandiForm([
        ['key' => 'lampiran', 'label' => 'Lampiran', 'type' => 'file', 'required' => true],
    ]);

    $id = srikandiNaskah('400.2.5/KH/272/405.10/2026', '2026-05-10');

    Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('form_id', $form->id)
        ->set('source', 'srikandi')
        ->set('nomor_naskah', '400.2.5/KH/272/405.10/2026')
        ->call('cariSurat')
        ->call('gunakanSurat', $id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('rakaca.guest-dashboard.index'));

    $submission = RakacaSubmission::firstOrFail();

    expect($submission->status)->toBe(SubmissionStatus::SiapDireview)
        ->and($submission->files_finalized_at)->not->toBeNull()
        ->and($submission->items['srikandi']['nomor_naskah'])->toBe('400.2.5/KH/272/405.10/2026')
        ->and($submission->uploads()->count())->toBe(0);
});

it('menolak pengajuan tanpa berkas upload dan tanpa surat', function () {
    Storage::fake('public');

    Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('form_id', srikandiForm()->id)
        ->call('save')
        ->assertHasErrors(['source']);

    Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('form_id', srikandiForm()->id)
        ->set('source', 'srikandi')
        ->set('nomor_naskah', '400.2.5/KH/272/405.10/2026')
        ->call('save')
        ->assertHasErrors(['source']);

    expect(RakacaSubmission::count())->toBe(0);
});

it('tetap menyimpan pengajuan upload biasa sebagai menunggu berkas', function () {
    Storage::fake('public');
    Storage::fake('local');

    Livewire::actingAs(User::factory()->create())
        ->test(Create::class)
        ->set('form_id', srikandiForm()->id)
        ->set('uploads', [UploadedFile::fake()->create('berkas.pdf', 10, 'application/pdf')])
        ->call('save')
        ->assertHasNoErrors();

    expect(RakacaSubmission::firstOrFail()->status)->toBe(SubmissionStatus::MenungguBerkas);
});

it('mempertahankan salinan surat saat form srikandi disimpan ulang', function () {
    $user = User::factory()->create();
    $form = srikandiForm([
        ['key' => 'lampiran', 'label' => 'Lampiran', 'type' => 'file', 'required' => true],
    ]);
    $id = srikandiNaskah('400.3/KH/9/2026', '2026-06-01');

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('form_id', $form->id)
        ->set('source', 'srikandi')
        ->set('nomor_naskah', '400.3/KH/9/2026')
        ->call('cariSurat')
        ->call('gunakanSurat', $id)
        ->call('save')
        ->assertHasNoErrors();

    $submission = RakacaSubmission::firstOrFail();

    // Edit hanya bisa dibuka saat Menunggu Berkas; paksa kembali untuk menguji snapshot.
    $submission->update(['status' => SubmissionStatus::MenungguBerkas, 'files_finalized_at' => null]);

    Livewire::actingAs($user)
        ->test(Edit::class, ['submission' => $submission])
        ->assertSet('surat.nomor_naskah', '400.3/KH/9/2026')
        ->call('saveItems')
        ->assertHasNoErrors();

    expect($submission->fresh()->items['srikandi']['nomor_naskah'])->toBe('400.3/KH/9/2026');
});
