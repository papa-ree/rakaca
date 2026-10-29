<?php

namespace Paparee\Rakaca\Tests\Unit;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Paparee\Rakaca\Support\OverviewPeriod;

// Notice periode memakai __(); skeleton Testbench belum punya binding translator.
beforeEach(function () {
    app()->singleton('translator', fn () => new Translator(new ArrayLoader, 'en'));
});

it('memakai 30 hari terakhir ketika input tanggal kosong atau rusak', function () {
    foreach ([[null, null], ['', ''], ['bukan-tanggal', '2026-01-10']] as [$dari, $sampai]) {
        $period = OverviewPeriod::fromQuery($dari, $sampai);

        expect($period->days())->toBe(OverviewPeriod::DEFAULT_DAYS)
            ->and($period->notice)->toBeNull()
            ->and($period->end->isToday())->toBeTrue();
    }
});

it('membalik rentang yang tertukar dan memberi notice', function () {
    $period = OverviewPeriod::fromQuery('2026-01-20', '2026-01-10');

    expect($period->start->toDateString())->toBe('2026-01-10')
        ->and($period->end->toDateString())->toBe('2026-01-20')
        ->and($period->days())->toBe(11)
        ->and($period->notice)->not->toBeNull();
});

it('memotong rentang lebih dari 90 hari dan memberi notice', function () {
    $period = OverviewPeriod::fromQuery('2026-01-01', '2026-12-31');

    expect($period->start->toDateString())->toBe('2026-01-01')
        ->and($period->end->toDateString())->toBe('2026-03-31')
        ->and($period->days())->toBe(OverviewPeriod::MAX_DAYS)
        ->and($period->notice)->not->toBeNull();
});

it('membatasi lastDays antara 1 dan MAX_DAYS', function () {
    expect(OverviewPeriod::lastDays(0)->days())->toBe(1)
        ->and(OverviewPeriod::lastDays(30)->days())->toBe(30)
        ->and(OverviewPeriod::lastDays(100)->days())->toBe(OverviewPeriod::MAX_DAYS);
});

it('memakai akhir hari sebagai batas atas agar hari terakhir ikut terhitung', function () {
    $period = OverviewPeriod::fromQuery('2026-01-01', '2026-01-31');

    expect($period->start->format('H:i:s'))->toBe('00:00:00')
        ->and($period->end->format('H:i:s'))->toBe('23:59:59')
        ->and($period->days())->toBe(31);
});
