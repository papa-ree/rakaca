<?php

namespace Paparee\Rakaca\Tests\Feature\Livewire\Guest;

use Livewire\Livewire;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Paparee\Rakaca\Livewire\Pages\Guest\Aduan\Index;
use Paparee\Rakaca\Models\Aduan;
use Paparee\Rakaca\Models\AduanCategory;

beforeEach(function () {
    RecaptchaV3::shouldReceive('verify')->andReturn(1.0)->byDefault();
    RecaptchaV3::shouldReceive('initJs')->andReturn('<script>mock</script>')->byDefault();
    RecaptchaV3::shouldReceive('field')->andReturn('<input type="hidden" name="g-recaptcha-response" value="test-token">')->byDefault();
});

it('aduan form retain input saat token expire (hanya error recaptcha)', function () {
    $category = AduanCategory::create(['name' => 'SSO']);

    // Simulate valid recaptcha initially, then expire (mock returns low score on submit)
    // First, test that when recaptcha fails, other fields retain and only recaptcha error appears
    RecaptchaV3::shouldReceive('verify')->andReturn(0.1); // low score = fail

    $response = Livewire::test(Index::class)
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

    expect(Aduan::count())->toBe(0);
});

it('mencegah concurrent submission melebihi rate limit', function () {
    $category = AduanCategory::create(['name' => 'Concurrent']);

    // Use default mock (score 1.0)
    RecaptchaV3::shouldReceive('verify')->andReturn(1.0);

    $submit = fn () => Livewire::test(Index::class)
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
    expect(Aduan::count())->toBe($max);
});
