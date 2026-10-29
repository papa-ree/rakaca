@php
    use Illuminate\Support\Carbon;
    use Paparee\Rakaca\Enums\SubmissionStatus;

    // Class ditulis utuh, bukan dirakit: Tailwind hanya memindai string literal.
    $meta = [
        SubmissionStatus::MenungguBerkas->value => [
            'icon' => 'clock',
            'badge' => 'Menunggu',
            'iconBg' => 'bg-gradient-to-br from-amber-500 to-amber-600 shadow-md shadow-amber-500/30 dark:shadow-amber-500/20',
            'pill' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'blob' => 'bg-amber-500/5',
        ],
        SubmissionStatus::SiapDireview->value => [
            'icon' => 'inbox',
            'badge' => 'Review',
            'iconBg' => 'bg-gradient-to-br from-sky-500 to-sky-600 shadow-md shadow-sky-500/30 dark:shadow-sky-500/20',
            'pill' => 'bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
            'blob' => 'bg-sky-500/5',
        ],
        SubmissionStatus::Diproses->value => [
            'icon' => 'loader',
            'badge' => 'Proses',
            'iconBg' => 'bg-gradient-to-br from-violet-500 to-purple-600 shadow-md shadow-violet-500/30 dark:shadow-violet-500/20',
            'pill' => 'bg-violet-50 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
            'blob' => 'bg-violet-500/5',
        ],
        SubmissionStatus::MenungguRevisi->value => [
            'icon' => 'undo-2',
            'badge' => 'Revisi',
            'iconBg' => 'bg-gradient-to-br from-rose-500 to-rose-600 shadow-md shadow-rose-500/30 dark:shadow-rose-500/20',
            'pill' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
            'blob' => 'bg-rose-500/5',
        ],
    ];

    $monthLabels = array_keys($monthly);
    $monthLabels = array_map(
        fn (string $ym): string => Carbon::createFromFormat('Y-m', $ym)->locale('id')->translatedFormat('M Y'),
        $monthLabels,
    );

    $serviceLabels = $services->pluck('service')->all();
    $serviceTotals = $services->pluck('total')->map(fn ($total): int => (int) $total)->all();

    $pieColors = ['#0d9488', '#10b981', '#0ea5e9', '#f59e0b', '#f43f5e', '#8b5cf6', '#64748b'];
@endphp

<div class="space-y-6">

    {{-- ========== Hero + Filter ========== --}}
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 via-teal-700 to-emerald-800 p-6 text-white shadow-xl dark:from-teal-800 dark:via-teal-900 dark:to-emerald-950">
        <div class="pointer-events-none absolute -right-12 -top-12 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
        <div class="pointer-events-none absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-emerald-400/20 blur-2xl"></div>
        <div class="pointer-events-none absolute bottom-0 right-24 h-24 w-24 rounded-full bg-teal-300/15 blur-xl"></div>

        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
            <div class="flex items-start gap-4 sm:items-center">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/15 shadow-inner ring-1 ring-white/20 backdrop-blur-sm">
                    <x-lucide-layout-dashboard class="size-6 text-white" />
                </div>

                <div class="min-w-0">
                    <h1 class="text-xl font-bold tracking-tight text-white sm:text-2xl">
                        {{ __('Overview Pengajuan') }}
                    </h1>
                    <p class="mt-0.5 text-sm text-teal-100/80">
                        {{ __('Pantau pengajuan yang masuk dan tentukan yang perlu ditindaklanjuti.') }}
                    </p>

                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-teal-100/70">
                        <span class="inline-flex items-center gap-1.5">
                            <x-lucide-calendar-range class="size-3.5" />
                            {{ $periodeLabel }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <x-lucide-clock class="size-3.5" />
                            {{ __(':days hari', ['days' => $activeDays]) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="w-full shrink-0 space-y-3 lg:w-[26rem]">
                {{-- Preset: optimistic, tombol aktif berubah sebelum server menjawab --}}
                <div class="flex items-center gap-1 rounded-xl bg-white/10 p-1 ring-1 ring-white/15"
                    x-data="{ days: {{ $activeDays }} }">
                    @foreach ([7, 30, 90] as $preset)
                        <button type="button" x-on:click="days = {{ $preset }}; $wire.setPreset({{ $preset }})"
                            :class="days === {{ $preset }}
                                ? 'bg-white text-teal-800 shadow-sm'
                                : 'text-teal-50 hover:bg-white/10'"
                            class="flex-1 rounded-lg px-3 py-1.5 text-xs font-semibold transition-all duration-150">
                            {{ $preset }} {{ __('hari') }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    <x-core::date-picker model="dari" mode="date" live
                        aria-label="{{ __('Dari tanggal') }}" placeholder="{{ __('Dari tanggal') }}" />

                    {{-- Di-remount saat tanggal awal berubah: batas kalender dikirim server dan picker ada di dalam wire:ignore. --}}
                    <div wire:key="overview-sampai-{{ $dari }}">
                        <x-core::date-picker model="sampai" mode="date" live
                            :minDate="$dari ?: null" :maxDate="$sampaiMaxDate"
                            aria-label="{{ __('Sampai tanggal') }}" placeholder="{{ __('Sampai tanggal') }}" />
                    </div>
                </div>

                <p class="text-[11px] text-teal-100/60">
                    {{ __('Rentang maksimal :days hari, dihitung inklusif.', ['days' => 90]) }}
                </p>

                @if ($periodError)
                    <p class="rounded-lg bg-amber-400/20 px-3 py-2 text-[11px] text-amber-50 ring-1 ring-amber-300/30">
                        {{ $periodError }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    {{-- ========== Skeleton ========== --}}
    <div wire:loading wire:target="updatedDari,updatedSampai,setPreset" class="space-y-4">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/[0.06] dark:bg-gray-900">
                    <div class="flex items-start justify-between gap-3">
                        <div class="h-11 w-11 animate-pulse rounded-xl bg-gray-100 dark:bg-gray-800"></div>
                        <div class="h-5 w-14 animate-pulse rounded-full bg-gray-100 dark:bg-gray-800"></div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <div class="h-8 w-20 animate-pulse rounded-lg bg-gray-100 dark:bg-gray-800"></div>
                        <div class="h-3 w-28 animate-pulse rounded bg-gray-100 dark:bg-gray-800"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div wire:loading.remove wire:target="updatedDari,updatedSampai,setPreset" class="space-y-6">

        {{-- ========== Statistik per status ========== --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $row)
                @php $m = $meta[$row['status']->value]; @endphp

                <div
                    class="group relative overflow-hidden rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md dark:border-white/[0.06] dark:bg-gray-900">
                    <div class="flex items-start justify-between">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl {{ $m['iconBg'] }}">
                            <x-dynamic-component :component="'lucide-'.$m['icon']" class="size-5 text-white" />
                        </div>

                        <span
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $m['pill'] }}">
                            {{ $m['badge'] }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="text-3xl font-bold tracking-tight text-gray-900 tabular-nums dark:text-white">
                            {{ number_format($row['total']) }}
                        </div>
                        <div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">
                            {{ __($row['status']->label()) }}
                        </div>
                        <div class="mt-0.5 text-[11px] text-gray-400 dark:text-gray-500">
                            {{ __($row['status']->description()) }}
                        </div>
                    </div>

                    <div
                        class="pointer-events-none absolute -bottom-4 -right-4 h-20 w-20 rounded-full transition-all duration-300 group-hover:scale-150 {{ $m['blob'] }}">
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ========== Grafik ========== --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

            {{-- Pie: layanan sepanjang waktu --}}
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm dark:border-white/[0.06] dark:bg-gray-900">
                <div
                    class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 dark:border-white/[0.06] sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-md shadow-teal-500/25 dark:shadow-teal-500/20">
                            <x-lucide-pie-chart class="size-4 text-white" />
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ __('Pengajuan per Layanan') }}
                            </h2>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Distribusi seluruh pengajuan.') }}
                            </p>
                        </div>
                    </div>
                    <span
                        class="inline-flex shrink-0 items-center self-start rounded-full bg-teal-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-teal-700 dark:bg-teal-900/30 dark:text-teal-300 sm:self-auto">
                        {{ __('Sepanjang waktu') }}
                    </span>
                </div>

                <div class="p-4 sm:p-5">
                    @if (empty($serviceTotals))
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <span
                                class="flex size-12 items-center justify-center rounded-full bg-teal-50 text-teal-400 dark:bg-teal-900/20 dark:text-teal-500">
                                <x-lucide-pie-chart class="size-6" />
                            </span>
                            <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ __('Belum ada pengajuan.') }}
                            </p>
                        </div>
                    @else
                        <x-core::chart type="pie" :labels="$serviceLabels" :datasets="[[
                            'data' => $serviceTotals,
                            'backgroundColor' => array_slice($pieColors, 0, count($serviceTotals)),
                            'borderWidth' => 0,
                        ]]" />
                    @endif
                </div>
            </div>

            {{-- Line: per bulan dalam periode --}}
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm dark:border-white/[0.06] dark:bg-gray-900">
                <div
                    class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 dark:border-white/[0.06] sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-teal-600 shadow-md shadow-sky-500/25 dark:shadow-sky-500/20">
                            <x-lucide-line-chart class="size-4 text-white" />
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ __('Pengajuan per Bulan') }}
                            </h2>
                            <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Dalam periode terpilih.') }}
                            </p>
                        </div>
                    </div>
                    <span
                        class="inline-flex shrink-0 items-center self-start rounded-full bg-sky-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-sky-700 dark:bg-sky-900/30 dark:text-sky-300 sm:self-auto">
                        {{ $periodeLabel }}
                    </span>
                </div>

                <div class="p-4 sm:p-5">
                    <x-core::chart type="line" :labels="$monthLabels" :datasets="[[
                        'label' => __('Pengajuan'),
                        'data' => array_values($monthly),
                        'borderColor' => '#0d9488',
                        'backgroundColor' => 'rgba(13, 148, 136, 0.15)',
                        'pointBackgroundColor' => '#0d9488',
                        'fill' => true,
                        'tension' => 0.35,
                    ]]" />
                </div>
            </div>
        </div>

        {{-- ========== Pengajuan terbaru ========== --}}
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm dark:border-white/[0.06] dark:bg-gray-900">
            <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 dark:border-white/[0.06] sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-md shadow-teal-500/25 dark:shadow-teal-500/20">
                        <x-lucide-inbox class="size-4 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __('Pengajuan Terbaru') }}
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Klik untuk membuka detail dan menindaklanjuti.') }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('rakaca.landlord.submission.index') }}" wire:navigate
                    class="inline-flex shrink-0 items-center gap-1.5 self-start rounded-lg px-3 py-1.5 text-xs font-semibold text-teal-700 transition hover:bg-teal-50 dark:text-teal-300 dark:hover:bg-teal-900/30 sm:self-auto">
                    {{ __('Lihat semua') }}
                    <x-lucide-arrow-right class="size-3.5" />
                </a>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-white/[0.06]">
                @forelse ($recent as $item)
                    <a href="{{ route('rakaca.landlord.submission.detail', $item->id) }}" wire:navigate
                        class="group flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-gray-50/80 dark:hover:bg-gray-800/50">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $item->code }} · {{ $item->form?->name ?? __('Tanpa form') }}
                            </p>
                            <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $item->form?->service?->name ?? '—' }}
                                · {{ $item->user?->name ?? '—' }}
                                · {{ $item->created_at?->translatedFormat('d M Y H:i') }}
                            </p>
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <span
                                class="inline-flex items-center rounded-md border border-gray-100 bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-700 dark:border-white/10 dark:bg-gray-800 dark:text-gray-300">
                                {{ __($item->statusLabel) }}
                            </span>
                            <x-lucide-chevron-right
                                class="size-4 text-gray-300 transition group-hover:text-teal-600 dark:text-gray-600" />
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <span
                            class="flex size-12 items-center justify-center rounded-full bg-teal-50 text-teal-400 dark:bg-teal-900/20 dark:text-teal-500">
                            <x-lucide-inbox class="size-6" />
                        </span>
                        <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('Belum ada pengajuan.') }}
                        </p>
                        <p class="mt-1 max-w-sm text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Pengajuan yang masuk akan muncul di sini.') }}
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
