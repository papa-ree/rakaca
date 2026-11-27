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
        return $status->badgeClass();
    }
}
