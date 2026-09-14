<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Create as SubmissionCreate;
use Paparee\Rakaca\Models\Form as FormModel;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

beforeEach(function () {
    RecaptchaV3::shouldReceive('verify')->andReturn(1.0)->byDefault();
    RecaptchaV3::shouldReceive('initJs')->andReturn('<script>mock</script>')->byDefault();
    RecaptchaV3::shouldReceive('field')->andReturn('<input type="hidden" name="g-recaptcha-response" value="test-token">')->byDefault();
});

it('memvalidasi file upload pada form dinamis', function () {
    Storage::fake('public');

    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Service File',
        'slug' => 'service-file',
        'actived' => true,
    ]);

    $form = FormModel::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form File',
        'slug' => 'form-file',
        'meta' => [
            'fields' => [
                [
                    'key' => 'dokumen',
                    'label' => 'Dokumen',
                    'type' => 'file',
                    'required' => true,
                    'placeholder' => '',
                    'options' => [],
                    'order' => 1,
                ],
            ],
        ],
        'actived' => true,
    ]);

    $user = \App\Models\User::factory()->create();

    // Test without file should fail validation (required)
    Livewire::actingAs($user)
        ->test(SubmissionCreate::class)
        ->set('form_id', $form->id)
        ->set('items.dokumen', null)
        ->call('save')
        ->assertHasErrors(['items.dokumen']);

    // Test with valid file should pass
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    Livewire::actingAs($user)
        ->test(SubmissionCreate::class)
        ->set('form_id', $form->id)
        ->set('items.dokumen', $file)
        ->call('save')
        ->assertHasNoErrors();

    $submission = RakacaSubmission::where('rakaca_form_id', $form->id)->first();
    expect($submission)->not->toBeNull();
    // File path should be stored
    expect($submission->items['data']['dokumen'])->toContain('rakaca-submissions');
    Storage::disk('public')->assertExists($submission->items['data']['dokumen']);
});

it('menolak file dengan mime tidak diizinkan', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Service Mime',
        'slug' => 'service-mime',
        'actived' => true,
    ]);
    $form = FormModel::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Mime',
        'slug' => 'form-mime',
        'meta' => [
            'fields' => [
                [
                    'key' => 'dokumen',
                    'label' => 'Dokumen',
                    'type' => 'file',
                    'required' => false,
                    'placeholder' => '',
                    'options' => [],
                    'order' => 1,
                ],
            ],
        ],
        'actived' => true,
    ]);
    $user = \App\Models\User::factory()->create();
    $badFile = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');

    Livewire::actingAs($user)
        ->test(SubmissionCreate::class)
        ->set('form_id', $form->id)
        ->set('items.dokumen', $badFile)
        ->call('save')
        ->assertHasErrors(['items.dokumen']);
});

it('memvalidasi select options sesuai daftar', function () {
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Service Select',
        'slug' => 'service-select',
        'actived' => true,
    ]);
    $form = FormModel::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Select',
        'slug' => 'form-select',
        'meta' => [
            'fields' => [
                [
                    'key' => 'kategori',
                    'label' => 'Kategori',
                    'type' => 'select',
                    'required' => true,
                    'placeholder' => '',
                    'options' => ['Option A', 'Option B'],
                    'order' => 1,
                ],
            ],
        ],
        'actived' => true,
    ]);
    $user = \App\Models\User::factory()->create();

    // Valid option should pass
    Livewire::actingAs($user)
        ->test(SubmissionCreate::class)
        ->set('form_id', $form->id)
        ->set('items.kategori', 'Option A')
        ->call('save')
        ->assertHasNoErrors();

    // Invalid option should fail
    Livewire::actingAs($user)
        ->test(SubmissionCreate::class)
        ->set('form_id', $form->id)
        ->set('items.kategori', 'Invalid Option')
        ->call('save')
        ->assertHasErrors(['items.kategori']);
});

it('aduan form retain input saat token expire (hanya error recaptcha)', function () {
    $category = \Paparee\Rakaca\Models\AduanCategory::create(['name' => 'SSO']);

    // Simulate valid recaptcha initially, then expire (mock returns low score on submit)
    // First, test that when recaptcha fails, other fields retain and only recaptcha error appears
    RecaptchaV3::shouldReceive('verify')->andReturn(0.1); // low score = fail

    $response = Livewire::test(\Paparee\Rakaca\Livewire\Pages\Guest\Aduan\Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 7 890')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Password SSO tidak bisa digunakan.')
        ->set('recaptchaToken', 'expired-token')
        ->call('submit');

    $response->assertHasErrors(['recaptchaToken']);
    // Other fields should not have errors (retain input)
    $response->assertHasNoErrors(['nama_lengkap', 'nip', 'wa_number', 'aduan_category_id', 'deskripsi']);

    expect(\Paparee\Rakaca\Models\Aduan::count())->toBe(0);
});

it('mencegah concurrent submission melebihi rate limit', function () {
    $category = \Paparee\Rakaca\Models\AduanCategory::create(['name' => 'Concurrent']);

    // Use default mock (score 1.0)
    RecaptchaV3::shouldReceive('verify')->andReturn(1.0);

    $submit = fn () => Livewire::test(\Paparee\Rakaca\Livewire\Pages\Guest\Aduan\Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 7 890')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Test concurrent.')
        ->set('recaptchaToken', 'test-token')
        ->call('submit');

    $max = config('rakaca.aduan.max_attempts', 5);
    foreach (range(1, $max) as $i) {
        $submit()->assertHasNoErrors();
    }

    $last = $submit();
    $last->assertHasErrors(['nama_lengkap']);
    expect(\Paparee\Rakaca\Models\Aduan::count())->toBe($max);
});
