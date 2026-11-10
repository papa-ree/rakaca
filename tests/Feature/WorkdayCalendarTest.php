<?php

use Illuminate\Support\Carbon;
use Paparee\Rakaca\Support\WorkdayCalendar;

function workday(string $value): Carbon
{
    return Carbon::parse($value, 'Asia/Jakarta');
}

it('menghitung akhir hari kerja ke-N dan melewati akhir pekan', function () {
    // Senin 9 Nov 2026 14.00 + 2 hari kerja → Rabu 11 Nov 2026 15.45
    $due = WorkdayCalendar::deadline(workday('2026-11-09 14:00'), 2);

    expect($due->format('Y-m-d H:i'))->toBe('2026-11-11 15:45');
});

it('memulai hitungan pada hari kerja berikutnya bila mulai setelah jam kerja', function () {
    // Jumat 16.00 (lewat 15.45) + 1 hari kerja → Selasa 15.45
    $due = WorkdayCalendar::deadline(workday('2026-11-13 16:00'), 1);

    expect($due->format('Y-m-d H:i'))->toBe('2026-11-17 15:45');
});

it('menambah menit kerja melewati jam pulang dan akhir pekan', function () {
    // Jumat 15.00 + 60 menit = 45 menit Jumat + 15 menit Senin
    $result = WorkdayCalendar::addWorkingMinutes(workday('2026-11-13 15:00'), 60);

    expect($result->format('Y-m-d H:i'))->toBe('2026-11-16 07:45');
});

it('menghitung menit kerja selama jeda lintas hari', function () {
    // Jumat 14.00–15.45 (105) + Senin 07.30–09.00 (90)
    $minutes = WorkdayCalendar::workingMinutes(workday('2026-11-13 14:00'), workday('2026-11-16 09:00'));

    expect($minutes)->toBe(195);
});
