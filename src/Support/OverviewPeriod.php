<?php

namespace Paparee\Rakaca\Support;

use Illuminate\Support\Carbon;

/**
 * Periode Overview Rakaca: rentang tanggal untuk grafik pengajuan.
 *
 * shortcut: logika ini meniru `Bale\Srikandi\Support\OverviewPeriod`.
 * Pindahkan ke `bale/core` bila muncul konsumen ketiga.
 *
 * Batas atas selalu AKHIR HARI. `created_at` di SQLite dibandingkan sebagai
 * string, jadi batas atas tanggal polos akan membuang pengajuan di hari
 * terakhir.
 */
final class OverviewPeriod
{
    /** Cukup lebar untuk satu kuartal, cukup sempit agar grafik tetap terbaca. */
    public const MAX_DAYS = 90;

    /** Default halaman: 30 hari terakhir. */
    public const DEFAULT_DAYS = 30;

    private function __construct(
        public readonly Carbon $start,
        public readonly Carbon $end,
        public readonly ?string $notice = null,
    ) {}

    /**
     * Input rusak tidak melempar exception: URL adalah input pengguna.
     * Setiap koreksi (dibalik, dipotong) meninggalkan `notice` supaya
     * pemotongan tidak pernah diam-diam.
     */
    public static function fromQuery(?string $dari, ?string $sampai): self
    {
        $start = self::parseDate($dari);
        $end = self::parseDate($sampai);

        if ($start === null || $end === null) {
            return self::lastDays(self::DEFAULT_DAYS);
        }

        $notice = null;

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end, $start];
            $notice = __('Tanggal awal dan akhir tertukar; urutannya dibalik.');
        }

        if ((int) $start->diffInDays($end) + 1 > self::MAX_DAYS) {
            $end = $start->copy()->addDays(self::MAX_DAYS - 1);
            $notice = __('Rentang dipotong menjadi :days hari.', ['days' => self::MAX_DAYS]);
        }

        return new self($start->startOfDay(), $end->endOfDay(), $notice);
    }

    public static function lastDays(int $days): self
    {
        $days = max(1, min($days, self::MAX_DAYS));

        return new self(
            now()->subDays($days - 1)->startOfDay(),
            now()->endOfDay(),
        );
    }

    public function days(): int
    {
        return (int) $this->start->diffInDays($this->end) + 1;
    }

    public function label(): string
    {
        if ($this->start->isSameDay($this->end)) {
            return $this->start->locale('id')->translatedFormat('d M Y');
        }

        return $this->start->locale('id')->translatedFormat('d M Y')
            .' – '
            .$this->end->locale('id')->translatedFormat('d M Y');
    }

    private static function parseDate(?string $value): ?Carbon
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        if (! Carbon::hasFormat($value, 'Y-m-d')) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $value);
    }
}
