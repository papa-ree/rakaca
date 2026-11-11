<div id="submissions" class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
    x-data="{ open: 0, toggle(index) { this.open = this.open === index ? null : index } }">
    {{-- Card header --}}
    <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                <x-lucide-clipboard-list class="size-4" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Status Pengajuan Terakhir') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Ketuk baris untuk melihat ringkasan status.') }}</p>
            </div>
        </div>

        <a href="{{ route('rakaca.guest.submission.index') }}" wire:navigate
            class="inline-flex shrink-0 items-center gap-1 self-start text-xs font-semibold text-purple-600 hover:underline sm:self-auto dark:text-purple-400">
            {{ __('Lihat semua') }}
            <x-lucide-arrow-right class="size-3.5" />
        </a>
    </div>

    <div class="p-4 sm:p-5">
        @if($this->submissions->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <x-lucide-inbox class="size-6" />
                </span>
                <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Belum ada pengajuan.') }}</p>
                <p class="mt-1 max-w-sm text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Pilih layanan di bawah, lalu kirim pengajuan pertama Anda.') }}
                </p>
                <x-core::button link :href="route('rakaca.guest.submission.create')" :label="__('Ajukan Layanan')" class="mt-4">
                    <x-slot name="icon"><x-lucide-plus class="size-4" /></x-slot>
                </x-core::button>
            </div>
        @else
            <div class="space-y-2">
                @foreach($this->submissions as $index => $submission)
                    @php
                        $status = $submission->status;
                        $service = $submission->form?->service;
                        $terminal = in_array($status, [\Paparee\Rakaca\Enums\SubmissionStatus::Ditolak, \Paparee\Rakaca\Enums\SubmissionStatus::Dibatalkan], true);
                        $stages = [__('Dikirim'), __('Direview'), __('Diproses'), __('Selesai')];
                        $current = $this->stage($status);
                    @endphp

                    <div wire:key="guest-submission-{{ $submission->id }}"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                        {{-- Header baris = tombol accordion --}}
                        <button type="button" x-on:click="toggle({{ $index }})"
                            x-bind:aria-expanded="open === {{ $index }} ? 'true' : 'false'"
                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="submission-panel-{{ $index }}"
                            class="flex w-full cursor-pointer items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-700/40">
                            <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300">
                                <x-dynamic-component :component="'lucide-' . ($service?->icon ?? 'layers')" class="size-4" />
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    <span class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $service?->name ?? __('Layanan TIK') }}
                                    </span>
                                    <span class="inline-flex shrink-0 items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $this->badgeClass($status) }}">
                                        {{ $status?->label() ?? __('Tidak diketahui') }}
                                    </span>
                                </span>
                                <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ $submission->form?->name ?? __('Pengajuan') }}
                                    <span class="text-gray-400 dark:text-gray-500">·</span>
                                    <span class="font-mono">#{{ $submission->code }}</span>
                                    <span class="text-gray-400 dark:text-gray-500">·</span>
                                    {{ $submission->created_at?->format('d M Y') }}
                                </span>
                            </span>

                            <x-lucide-chevron-down class="mt-1 size-4 shrink-0 text-gray-400 transition-transform duration-200 dark:text-gray-500"
                                x-bind:class="open === {{ $index }} ? 'rotate-180' : ''" />
                        </button>

                        {{-- Panel: tinggi dianimasikan lewat grid-template-rows (tanpa plugin Alpine).
                             Style ditulis inline agar tidak bergantung pada scanning class Tailwind.
                             `visibility: hidden` menjaga tautan panel tertutup keluar dari urutan fokus/AT. --}}
                        <div x-cloak class="grid transition-all duration-300 ease-out"
                            x-bind:style="open === {{ $index }}
                                ? 'display: grid; grid-template-rows: 1fr; opacity: 1; visibility: visible'
                                : 'display: grid; grid-template-rows: 0fr; opacity: 0; visibility: hidden'">
                            <div class="overflow-hidden" style="min-height: 0" x-bind:aria-hidden="open === {{ $index }} ? 'false' : 'true'">
                                <div id="submission-panel-{{ $index }}"
                                    class="border-t border-gray-100 bg-gray-50/60 px-4 py-3 dark:border-gray-700/70 dark:bg-gray-700/20">
                                    @if($terminal)
                                        <div class="flex items-start gap-2 rounded-lg border p-3 text-xs {{ $status === \Paparee\Rakaca\Enums\SubmissionStatus::Ditolak
                                                ? 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-800/40 dark:bg-rose-900/20 dark:text-rose-300'
                                                : 'border-gray-200 bg-gray-100 text-gray-600 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                            <x-lucide-info class="mt-0.5 size-3.5 shrink-0" />
                                            <span>{{ $status?->description() }}</span>
                                        </div>
                                    @else
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $status?->description() }}</p>

                                        {{-- Stepper: penanda posisi status, bukan riwayat waktu. --}}
                                        <ol class="mt-3 flex items-start">
                                            @foreach($stages as $step => $label)
                                                <li class="flex flex-1 items-start last:flex-none">
                                                    <div class="flex flex-col items-center gap-1.5">
                                                        <span class="flex size-6 items-center justify-center rounded-full border text-[10px] font-bold {{ $step <= $current
                                                                ? 'border-purple-600 bg-purple-600 text-white'
                                                                : 'border-gray-300 bg-white text-gray-400 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-500' }}">
                                                            @if($step < $current)
                                                                <x-lucide-check class="size-3.5" />
                                                            @else
                                                                {{ $step + 1 }}
                                                            @endif
                                                        </span>
                                                        <span class="whitespace-nowrap text-[10px] {{ $step <= $current
                                                                ? 'font-semibold text-gray-900 dark:text-white'
                                                                : 'text-gray-500 dark:text-gray-400' }}">
                                                            {{ $label }}
                                                        </span>
                                                    </div>

                                                    @unless($loop->last)
                                                        <span class="mx-2 mt-3 h-0.5 flex-1 rounded {{ $step < $current ? 'bg-purple-600' : 'bg-gray-200 dark:bg-gray-700' }}"></span>
                                                    @endunless
                                                </li>
                                            @endforeach
                                        </ol>
                                    @endif

                                    <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-gray-500 dark:text-gray-400">
                                        <span class="inline-flex items-center gap-1">
                                            <x-lucide-paperclip class="size-3" />
                                            {{ __(':count berkas', ['count' => $submission->uploads_count]) }}
                                        </span>
                                        @if($submission->status_changed_at)
                                            <span class="inline-flex items-center gap-1">
                                                <x-lucide-clock class="size-3" />
                                                {{ __('Diperbarui :date', ['date' => $submission->status_changed_at->format('d M Y H:i')]) }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex flex-col gap-2 sm:flex-row">
                                        <x-core::button link :href="route('rakaca.guest.submission.show', $submission->id)" :label="__('Lihat Detail')" class="w-full justify-center sm:w-auto">
                                            <x-slot name="icon"><x-lucide-file-search class="size-4" /></x-slot>
                                        </x-core::button>

                                        @if($status === \Paparee\Rakaca\Enums\SubmissionStatus::MenungguBerkas)
                                            <x-core::secondary-button link :href="route('rakaca.guest.submission.edit', $submission->id)" :label="__('Lengkapi Berkas')" class="w-full justify-center sm:w-auto" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
