<?php

namespace Paparee\Rakaca\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Satu-satunya tempat yang memutuskan di mana berkas pengajuan disimpan.
 *
 * Production → S3, selain itu → disk `public` (bisa diakses lewat /storage).
 * View wajib memakai `url()` di sini, bukan `Storage::url()` default, supaya
 * link ikut pindah disk saat environment berubah.
 */
final class SubmissionStorage
{
    public static function disk(): string
    {
        return app()->isProduction() ? 's3' : 'public';
    }

    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return Storage::disk(self::disk())->url($path);
    }
}
