<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus as Status;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Lazy]
class SubmissionStatus extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.submission-status');
    }

    public function placeholder()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.submission-status-skeleton');
    }

    /** Empat pengajuan terakhir; baris pertama tampil terbuka di dashboard. */
    #[Computed()]
    public function submissions()
    {
        return RakacaSubmission::with(['form.service'])
            ->withCount('uploads')
            ->whereUserUuid(auth()->user()->uuid)
            ->latest('created_at')
            ->limit(4)
            ->get();
    }

    /**
     * Posisi tahapan: 0 dikirim, 1 direview, 2 diproses, 3 selesai.
     * Tahapan bukan riwayat waktu — hanya penanda posisi status saat ini.
     */
    public function stage(Status $status): int
    {
        return match ($status) {
            Status::MenungguBerkas => 0,
            Status::SiapDireview, Status::MenungguRevisi => 1,
            Status::Diproses => 2,
            Status::Selesai => 3,
            default => 0,
        };
    }

    /** Warna badge memakai palet status Bale (bukan warna acak per enum). */
    public function badgeClass(Status $status): string
    {
        return match ($status) {
            Status::Selesai => 'bg-teal-50 text-teal-700 border-teal-100 dark:bg-teal-900/20 dark:text-teal-300 dark:border-teal-800/40',
            Status::Ditolak => 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-800/40',
            Status::Dibatalkan => 'bg-gray-100 text-gray-600 border-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600',
            Status::SiapDireview => 'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-900/20 dark:text-sky-300 dark:border-sky-800/40',
            Status::Diproses => 'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-300 dark:border-indigo-800/40',
            default => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800/40',
        };
    }
}
