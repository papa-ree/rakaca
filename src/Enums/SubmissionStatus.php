<?php

namespace Paparee\Rakaca\Enums;

enum SubmissionStatus: string
{
    case MenungguBerkas = 'menunggu-berkas';
    case SiapDireview = 'siap-direview';
    case Diproses = 'diproses';
    case MenungguRevisi = 'menunggu-revisi';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MenungguBerkas => 'Menunggu Berkas',
            self::SiapDireview => 'Siap Direview',
            self::Diproses => 'Diproses',
            self::MenungguRevisi => 'Menunggu Revisi',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MenungguBerkas => 'amber',
            self::SiapDireview => 'blue',
            self::Diproses => 'fuchsia',
            self::MenungguRevisi => 'orange',
            self::Selesai => 'green',
            self::Dibatalkan => 'slate',
            self::Ditolak => 'red',
        };
    }

    public function cancellableByUser(): bool
    {
        return in_array($this, [self::MenungguBerkas, self::SiapDireview], true);
    }

    public function description(): string
    {
        return match ($this) {
            self::MenungguBerkas => 'Silakan lengkapi berkas dan kirim untuk direview.',
            self::SiapDireview => 'Berkas sudah diterima dan menunggu review petugas.',
            self::Diproses => 'Pengajuan sedang diproses oleh petugas.',
            self::MenungguRevisi => 'Ada catatan revisi dari petugas, silakan perbaiki.',
            self::Selesai => 'Pengajuan telah selesai diproses.',
            self::Dibatalkan => 'Pengajuan dibatalkan.',
            self::Ditolak => 'Pengajuan ditolak oleh petugas.',
        };
    }

    /**
     * @return self[]
     */
    public static function allowedTransitions(self $from): array
    {
        return match ($from) {
            self::MenungguBerkas => [self::SiapDireview, self::Ditolak, self::Dibatalkan],
            self::SiapDireview => [self::Diproses, self::Ditolak, self::Dibatalkan],
            self::Diproses => [self::MenungguRevisi, self::Selesai, self::Ditolak],
            self::MenungguRevisi => [self::SiapDireview, self::Ditolak],
            default => [],
        };
    }

    public static function canTransition(self $from, self $to): bool
    {
        return in_array($to, self::allowedTransitions($from), true);
    }

    /**
     * Map legacy string values to canonical enum for backward-compat reads.
     */
    public static function fromLegacy(string $value): ?self
    {
        return match ($value) {
            'pending' => self::MenungguBerkas,
            'review' => self::SiapDireview,
            'approved' => self::Selesai,
            'rejected' => self::Ditolak,
            'ditutup' => self::Dibatalkan,
            default => self::tryFrom($value),
        };
    }
}
