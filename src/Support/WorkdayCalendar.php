<?php

namespace Paparee\Rakaca\Support;

use Illuminate\Support\Carbon;

/**
 * Kalender kerja SLA: Senin–Jumat 07.30–15.45 (dapat dikonfigurasi).
 *
 * Libur nasional belum dihitung — tambahkan di `isWorkday()` bila dibutuhkan.
 */
final class WorkdayCalendar
{
    /**
     * Akhir hari kerja ke-N dihitung dari tanggal mulai.
     *
     * Mulai Senin 14.00 dengan 2 hari kerja → Rabu 15.45. Bila mulai di luar
     * jam kerja (atau akhir pekan), hitungan baru dimulai pada hari kerja
     * berikutnya.
     */
    public static function deadline(Carbon $start, int $days): Carbon
    {
        $date = $start->copy();

        if (! self::isWorkday($date) || $date->gt(self::workEnd($date))) {
            $date = self::nextWorkday($date);
        }

        for ($remaining = max(1, $days); $remaining > 0; $remaining--) {
            $date = self::nextWorkday($date);
        }

        return self::workEnd($date);
    }

    /**
     * Jumlah menit kerja antara dua waktu (untuk menghitung lama jeda revisi).
     */
    public static function workingMinutes(Carbon $from, Carbon $to): int
    {
        if ($to->lte($from)) {
            return 0;
        }

        $cursor = $from->copy();
        $minutes = 0;

        while ($cursor->lt($to)) {
            if (! self::isWorkday($cursor) || $cursor->gte(self::workEnd($cursor))) {
                $cursor = self::workStart(self::nextWorkday($cursor));

                continue;
            }

            if ($cursor->lt(self::workStart($cursor))) {
                $cursor = self::workStart($cursor);
            }

            $chunkEnd = self::workEnd($cursor)->min($to);
            $minutes += (int) $cursor->diffInMinutes($chunkEnd);
            $cursor = $chunkEnd;
        }

        return $minutes;
    }

    /**
     * Maju sebanyak menit kerja dari satu titik waktu.
     */
    public static function addWorkingMinutes(Carbon $at, int $minutes): Carbon
    {
        $cursor = $at->copy();

        while ($minutes > 0) {
            if (! self::isWorkday($cursor) || $cursor->gte(self::workEnd($cursor))) {
                $cursor = self::workStart(self::nextWorkday($cursor));

                continue;
            }

            if ($cursor->lt(self::workStart($cursor))) {
                $cursor = self::workStart($cursor);
            }

            $available = (int) $cursor->diffInMinutes(self::workEnd($cursor));

            if ($available >= $minutes) {
                return $cursor->addMinutes($minutes);
            }

            $minutes -= $available;
            $cursor = self::workStart(self::nextWorkday($cursor));
        }

        return $cursor;
    }

    private static function isWorkday(Carbon $date): bool
    {
        return in_array($date->dayOfWeekIso, (array) config('rakaca.sla.work_days', [1, 2, 3, 4, 5]), true);
    }

    private static function workStart(Carbon $date): Carbon
    {
        return $date->copy()->setTime(...self::timeParts(config('rakaca.sla.work_start', '07:30')));
    }

    private static function workEnd(Carbon $date): Carbon
    {
        return $date->copy()->setTime(...self::timeParts(config('rakaca.sla.work_end', '15:45')));
    }

    private static function nextWorkday(Carbon $date): Carbon
    {
        $date = $date->copy()->addDay();

        while (! self::isWorkday($date)) {
            $date->addDay();
        }

        return $date;
    }

    /**
     * @return array{0: int, 1: int}
     */
    private static function timeParts(string $time): array
    {
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return [$hour, $minute];
    }
}
