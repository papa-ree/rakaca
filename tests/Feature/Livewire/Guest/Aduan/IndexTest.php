<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest\Aduan;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Crypt;
use Livewire\Livewire;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Paparee\Rakaca\Livewire\Pages\Guest\Aduan\Index;
use Paparee\Rakaca\Models\Aduan;
use Paparee\Rakaca\Models\AduanCategory;

beforeEach(function () {
    RecaptchaV3::shouldReceive('verify')
        ->andReturn(1.0)
        ->byDefault();
    RecaptchaV3::shouldReceive('initJs')
        ->andReturn('<script>/* mock recaptcha */</script>')
        ->byDefault();
    RecaptchaV3::shouldReceive('field')
        ->andReturn('<input type="hidden" name="g-recaptcha-response" value="test-token">')
        ->byDefault();
});

it('menampilkan halaman bantuan pada path /bantuan tanpa login', function () {
    AduanCategory::create(['name' => 'SSO']);

    $this->get('/bantuan')
        ->assertOk()
        ->assertSee('Bantuan')
        ->assertSee('Kirim ke WhatsApp')
        ->assertSee('SSO');
});

it('berhasil mengirim aduan, tersimpan dengan NIP & WA terenkripsi', function () {
    $category = AduanCategory::create(['name' => 'Aplikasi']);

    $response = Livewire::test(Index::class)
        ->set('nama_lengkap', '  Budi  Santoso  ')
        ->set('nip', '19700101 200003 1 123')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', "Login SSO saya gagal terus.\nPadahal password sudah benar.")
        ->set('recaptchaToken', 'test-token')
        ->call('submit');

    $response
        ->assertHasNoErrors()
        ->assertRedirectContains('https://wa.me/'.config('rakaca.whatsapp.support_number').'?text=')
        ->assertRedirectContains(rawurlencode('*Aduan Rakaca*'))
        ->assertRedirectContains(rawurlencode('*Kode Ref*: '))
        ->assertRedirectContains(rawurlencode('*Nama Lengkap*: Budi Santoso'))
        ->assertRedirectContains(rawurlencode('*NIP*: `197001012000031123`'))
        ->assertRedirectContains(rawurlencode('*Deskripsi*:'))
        ->assertRedirectContains(rawurlencode('Login SSO saya gagal terus.'));

    $aduan = Aduan::query()->first();

    expect($aduan)->not->toBeNull()
        ->and($aduan->ref_code)->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i')
        ->and(Aduan::query()->where('ref_code', $aduan->ref_code)->count())->toBe(1)
        ->and(Crypt::decryptString($aduan->getRawOriginal('nama_lengkap')))->toBe('Budi Santoso')
        ->and(Crypt::decryptString($aduan->getRawOriginal('nip')))->toBe('197001012000031123')
        ->and(Crypt::decryptString($aduan->getRawOriginal('wa_number')))->toBe('6281234567890')
        ->and($aduan->status)->toBe('pending');

    // Nomor WA pribadi tidak boleh ikut dikirim ke wa.me (privasi)
    expect($response->effects['redirect'] ?? '')
        ->not->toContain('081234567890')
        ->not->toContain('6281234567890')
        ->not->toContain(rawurlencode('*No. WhatsApp*:'));
});

it('menerima NIP dengan format mask (spasi) dan menyimpannya tanpa spasi', function () {
    $category = AduanCategory::create(['name' => 'SSO']);

    $response = Livewire::test(Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 7 890')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Password SSO tidak bisa digunakan.')
        ->set('recaptchaToken', 'test-token')
        ->call('submit');

    $response
        ->assertHasNoErrors()
        ->assertRedirectContains(rawurlencode('*NIP*: `123456781234567890`'));

    $aduan = Aduan::query()->first();

    expect($aduan)->not->toBeNull()
        ->and(Crypt::decryptString($aduan->getRawOriginal('nip')))->toBe('123456781234567890');
});

it('gagal menyimpan ketika NIP melebihi 21 karakter', function () {
    $category = AduanCategory::create(['name' => 'SSO']);

    Livewire::test(Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 1 2345')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Password SSO tidak bisa digunakan.')
        ->set('recaptchaToken', 'test-token')
        ->call('submit')
        ->assertHasErrors(['nip']);

    expect(Aduan::query()->count())->toBe(0);
});

it('menolak aduan ketika skor reCAPTCHA rendah', function () {
    RecaptchaV3::shouldReceive('verify')->andReturn(0.1);

    $category = AduanCategory::create(['name' => 'SSO']);

    Livewire::test(Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 7 890')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Password SSO tidak bisa digunakan.')
        ->set('recaptchaToken', 'test-token')
        ->call('submit')
        ->assertHasErrors(['recaptchaToken']);

    expect(Aduan::query()->count())->toBe(0);
});

it('menangani kegagalan jaringan reCAPTCHA tanpa error 500', function () {
    RecaptchaV3::shouldReceive('verify')
        ->andThrow(new ConnectException(
            'Connection error',
            new Request('POST', 'https://www.google.com/recaptcha/api/siteverify')
        ));

    $category = AduanCategory::create(['name' => 'SSO']);

    Livewire::test(Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 7 890')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Password SSO tidak bisa digunakan.')
        ->set('recaptchaToken', 'test-token')
        ->call('submit')
        ->assertHasErrors(['recaptchaToken']);

    expect(Aduan::query()->count())->toBe(0);
});

it('membatasi jumlah pengiriman aduan per IP', function () {
    $category = AduanCategory::create(['name' => 'SSO']);

    $submit = fn () => Livewire::test(Index::class)
        ->set('nama_lengkap', 'Budi Santoso')
        ->set('nip', '12345678 123456 7 890')
        ->set('wa_number', '081234567890')
        ->set('aduan_category_id', $category->id)
        ->set('deskripsi', 'Password SSO tidak bisa digunakan.')
        ->set('recaptchaToken', 'test-token')
        ->call('submit');

    foreach (range(1, config('rakaca.aduan.max_attempts')) as $i) {
        $submit()->assertHasNoErrors();
    }

    $submit()->assertHasErrors(['nama_lengkap']);

    expect(Aduan::query()->count())->toBe(config('rakaca.aduan.max_attempts'));
});
