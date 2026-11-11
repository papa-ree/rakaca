<?php

use Paparee\Rakaca\Support\ServicePalette;

it('menyediakan 12 token warna', function () {
    expect(ServicePalette::keys())->toHaveCount(12);
});

it('setiap token punya label dan class lengkap', function () {
    foreach (ServicePalette::TONES as $tone) {
        expect(array_keys($tone))->toBe(['label', 'icon', 'button', 'title', 'swatch'])
            ->and($tone['label'])->not->toBeEmpty()
            ->and($tone['icon'])->toContain('from-')
            ->and($tone['button'])->toContain('bg-')
            ->and($tone['title'])->toContain('group-hover:text-')
            ->and($tone['swatch'])->toContain('bg-');
    }
});

it('jatuh ke indigo untuk warna kosong atau tidak dikenal', function () {
    expect(ServicePalette::get(null))->toBe(ServicePalette::get('indigo'))
        ->and(ServicePalette::get('tidak-ada'))->toBe(ServicePalette::get('indigo'));
});
