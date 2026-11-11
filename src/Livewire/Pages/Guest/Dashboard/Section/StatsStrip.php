<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Section;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\PersonHasService;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Lazy]
class StatsStrip extends Component
{
    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.stats-strip');
    }

    public function placeholder()
    {
        return view('rakaca::livewire.pages.guest.dashboard.section.stats-strip-skeleton');
    }

    /**
     * Ringkasan angka guest: layanan aktif, pengajuan menunggu, pengajuan selesai.
     * Dihitung per-status agar nilai legacy (mis. `pending`) tetap terbaca.
     */
    #[Computed()]
    public function stats(): array
    {
        $byStatus = RakacaSubmission::whereUserUuid(auth()->user()->uuid)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $done = (int) ($byStatus[SubmissionStatus::Selesai->value] ?? 0);
        $closed = (int) ($byStatus[SubmissionStatus::Ditolak->value] ?? 0)
            + (int) ($byStatus[SubmissionStatus::Dibatalkan->value] ?? 0);

        return [
            'active' => PersonHasService::whereUserUuid(auth()->user()->uuid)->where('actived', true)->count(),
            'pending' => max(0, (int) $byStatus->sum() - $done - $closed),
            'done' => $done,
        ];
    }
}
