<?php

namespace Paparee\Rakaca\Support;

use Bale\Core\Support\Cdn;
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

    /**
     * Preview berkas di production lewat CDN, karena URL S3 mentah tidak bisa
     * di-embed (bucket privat / beda domain). Di lokal tetap pakai /storage.
     */
    public static function url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return app()->isProduction()
            ? Cdn::url($path)
            : Storage::disk('public')->url($path);
    }
}
