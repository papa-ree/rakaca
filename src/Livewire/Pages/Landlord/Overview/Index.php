<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Overview;

use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Support\OverviewPeriod;

#[Layout('rakaca::layouts.app')]
#[Title('Rakaca | Overview')]
class Index extends Component
{
    public string $dari = '';

    public string $sampai = '';

    public ?string $periodError = null;

    /** Status yang dianggap belum selesai (tidak masuk statistik bila selesai/ditolak/dibatalkan). */
    private const UNFINISHED = [
        SubmissionStatus::MenungguBerkas,
        SubmissionStatus::SiapDireview,
        SubmissionStatus::Diproses,
        SubmissionStatus::MenungguRevisi,
    ];

    /** Nilai status lama yang masih mungkin tersimpan di data produksi. */
    private const LEGACY = [
        'pending' => 'menunggu-berkas',
        'review' => 'siap-direview',
    ];

    public function mount(): void
    {
        if (! auth()->user()->can('submission.read')) {
            abort(403);
        }

        $this->applyPeriod(OverviewPeriod::fromQuery(
            request()->query('dari'),
            request()->query('sampai'),
        ));
    }

    public function updatedDari(): void
    {
        $this->applyPeriod($this->period());
    }

    public function updatedSampai(): void
    {
        $this->applyPeriod($this->period());
    }

    public function setPreset(int $days): void
    {
        $this->applyPeriod(OverviewPeriod::lastDays($days));
    }

    protected function period(): OverviewPeriod
    {
        return OverviewPeriod::fromQuery($this->dari, $this->sampai);
    }

    /**
     * Tulis kembali rentang yang BENAR-BENAR dipakai ke input, supaya
     * pemotongan/pembalikan di server terlihat di layar.
     */
    protected function applyPeriod(OverviewPeriod $period): void
    {
        $this->dari = $period->start->toDateString();
        $this->sampai = $period->end->toDateString();
        $this->periodError = $period->notice;
    }

    public function render()
    {
        $period = $this->period();

        return view('rakaca::livewire.pages.landlord.overview.index', [
            'period' => $period,
            'periodeLabel' => $period->label(),
            'activeDays' => $period->days(),
            // Batas kalender "sampai": 90 hari inklusif dari tanggal awal.
            'sampaiMaxDate' => $period->start->copy()
                ->addDays(OverviewPeriod::MAX_DAYS - 1)
                ->toDateString(),
            'stats' => $this->stats(),
            'sla' => $this->slaStats($period),
            'recent' => RakacaSubmission::with(['form.service', 'user'])
                ->latest('created_at')
                ->limit(8)
                ->get(),
            'services' => $this->perService($period),
            'monthly' => $this->monthly($period),
        ]);
    }

    /**
     * Statistik pengajuan yang belum selesai per status.
     *
     * @return array<int, array{status: SubmissionStatus, total: int}>
     */
    protected function stats(): array
    {
        $raw = RakacaSubmission::query()
            ->whereIn('status', array_merge(
                array_map(fn (SubmissionStatus $status): string => $status->value, self::UNFINISHED),
                array_keys(self::LEGACY),
            ))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $counts = [];

        foreach ($raw as $status => $total) {
            $status = self::LEGACY[$status] ?? $status;
            $counts[$status] = ($counts[$status] ?? 0) + (int) $total;
        }

        return array_map(fn (SubmissionStatus $status): array => [
            'status' => $status,
            'total' => $counts[$status->value] ?? 0,
        ], self::UNFINISHED);
    }

    /**
     * Kepatuhan SLA: hanya pengajuan yang selesai (resolved_at) dalam periode.
     *
     * @return array{total: int, on_time: int, late: int, compliance: ?int}
     */
    protected function slaStats(OverviewPeriod $period): array
    {
        $base = RakacaSubmission::query()
            ->join('rakaca_form_responses', 'rakaca_form_responses.rakaca_submission_id', '=', 'rakaca_submissions.id')
            ->whereNull('rakaca_form_responses.deleted_at')
            ->whereNotNull('rakaca_submissions.sla_due_at')
            ->whereBetween('rakaca_form_responses.resolved_at', [$period->start, $period->end]);

        $total = (clone $base)->count();
        $late = (clone $base)
            ->whereColumn('rakaca_form_responses.resolved_at', '>', 'rakaca_submissions.sla_due_at')
            ->count();

        return [
            'total' => $total,
            'on_time' => $total - $late,
            'late' => $late,
            'compliance' => $total > 0 ? (int) round(($total - $late) / $total * 100) : null,
        ];
    }

    /**
     * Distribusi pengajuan per layanan dalam periode terpilih.
     *
     * @return Collection<int, object>
     */
    protected function perService(OverviewPeriod $period)
    {
        return RakacaSubmission::query()
            ->join('rakaca_forms', 'rakaca_forms.id', '=', 'rakaca_submissions.rakaca_form_id')
            ->join('rakaca_services', 'rakaca_services.id', '=', 'rakaca_forms.rakaca_service_id')
            ->whereNull('rakaca_forms.deleted_at')
            ->whereNull('rakaca_submissions.deleted_at')
            ->whereBetween('rakaca_submissions.created_at', [$period->start, $period->end])
            ->selectRaw('rakaca_services.name as service, count(*) as total')
            ->groupBy('rakaca_services.name')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Pengajuan per bulan dalam periode terpilih. Dihitung per bulan
     * (maksimal 4 query) supaya tidak bergantung pada fungsi tanggal
     * yang berbeda antara MySQL dan SQLite.
     *
     * @return array<string, int> key `Y-m`, bulan kosong tetap ada
     */
    protected function monthly(OverviewPeriod $period): array
    {
        $counts = [];
        $cursor = $period->start->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($period->end)) {
            $from = $cursor->copy()->max($period->start);
            $to = $cursor->copy()->endOfMonth()->min($period->end);

            $counts[$cursor->format('Y-m')] = RakacaSubmission::query()
                ->whereBetween('created_at', [$from, $to])
                ->count();

            $cursor->addMonthNoOverflow();
        }

        return $counts;
    }
}
