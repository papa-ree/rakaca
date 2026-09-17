<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    @php
        $status = $submission->status instanceof \Paparee\Rakaca\Enums\SubmissionStatus
            ? $submission->status
            : \Paparee\Rakaca\Enums\SubmissionStatus::fromLegacy($submission->status);
        $items = $submission->items['data'] ?? [];
    @endphp

    <x-core::breadcrumb :items="[
        ['label' => __('Pengajuan'), 'route' => 'rakaca.guest.submission.index'],
    ]" :active="__('Detail Pengajuan')" />

    <div class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            {{-- Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                    <div class="flex items-start gap-3.5">
                        <div class="hidden sm:flex p-3 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg shrink-0">
                            <x-lucide-file-text class="w-6 h-6 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">{{ $submission->form?->name ?? __('Pengajuan') }}</h2>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-mono text-xs bg-white dark:bg-gray-700 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">#{{ $submission->code }}</span>
                                <span class="mx-1">·</span> {{ $submission->created_at->format('d M Y H:i') }}
                                <span class="ml-2 px-3 py-1 text-xs font-semibold text-{{ $status->color() }}-700 bg-{{ $status->color() }}-100 rounded-full dark:bg-{{ $status->color() }}-900/50 dark:text-{{ $status->color() }}-300 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $status->color() }}-500 animate-pulse"></span>
                                    {{ $status->label() }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Reservation data --}}
                    @if($items)
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                                <x-lucide-list class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                {{ __('Data Pengajuan') }}
                            </h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if($submission->form && $submission->form->meta && isset($submission->form->meta['fields']))
                                    @foreach($submission->form->meta['fields'] as $field)
                                        <div class="p-3.5 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700/80">
                                            <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ $field['label'] }}</dt>
                                            <dd class="text-sm font-medium text-gray-900 dark:text-white break-words">
                                                @php $val = $items[$field['key']] ?? null; @endphp
                                                @if(($field['type'] ?? '') === 'file' && is_string($val) && $val)
                                                    <a href="{{ Storage::url($val) }}" target="_blank" class="inline-flex items-center gap-1.5 text-purple-600 dark:text-purple-400 hover:underline">
                                                        <x-lucide-download class="w-4 h-4" /> {{ __('Berkas') }}
                                                    </a>
                                                @elseif(is_array($val))
                                                    {{ implode(', ', $val) }}
                                                @elseif($val !== null && $val !== '')
                                                    {{ $val }}
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-500">—</span>
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                @endif
                            </dl>
                        </div>
                    @endif

                    {{-- Uploads --}}
                    @if($submission->uploads && $submission->uploads->count() > 0)
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <x-lucide-paperclip class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                {{ __('Berkas Pendukung') }}
                            </h3>
                            <div class="grid gap-2.5">
                                @foreach($submission->uploads as $up)
                                    <a href="{{ Storage::url($up->file_path) }}" target="_blank"
                                        class="flex items-center justify-between gap-3 p-3 bg-amber-50/50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-800/20 hover:border-purple-300 dark:hover:border-purple-700 transition">
                                        <span class="flex items-center gap-2.5 min-w-0">
                                            <x-lucide-file-text class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" />
                                            <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $up->original_name }}</span>
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 shrink-0 font-mono">{{ number_format($up->size / 1024, 1) }} KB</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Result / Response --}}
                    @if($submission->response && $submission->response->resolution_data)
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                                <x-lucide-check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                {{ __('Hasil Layanan') }}
                            </h3>
                            <div class="p-4 bg-emerald-50/60 dark:bg-emerald-900/10 rounded-2xl border border-emerald-200 dark:border-emerald-800/30 space-y-3">
                                @foreach($submission->response->resolution_data as $key => $val)
                                    @if(is_string($val) && $val)
                                        <div>
                                            <dt class="text-xs font-semibold text-emerald-800 dark:text-emerald-300 mb-0.5">{{ Str::headline($key) }}</dt>
                                            <dd class="text-sm text-gray-900 dark:text-white break-words">{{ $val }}</dd>
                                        </div>
                                    @endif
                                @endforeach
                                @if($submission->response->resolvedBy)
                                    <p class="pt-2 text-xs text-gray-500 dark:text-gray-400 border-t border-emerald-100 dark:border-emerald-800/20">
                                        {{ __('Diproses oleh') }} {{ $submission->response->resolvedBy->name ?? $submission->response->resolvedBy->username ?? '-' }}
                                        · {{ optional($submission->response->resolved_at)->format('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md lg:sticky lg:top-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md">
                            <x-lucide-activity class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ __('Status Tiket') }}</h3>
                            <p class="text-xs font-mono text-purple-600 dark:text-purple-400 font-medium truncate">#{{ $submission->code }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="px-3 py-1 text-xs font-semibold text-{{ $status->color() }}-700 bg-{{ $status->color() }}-100 rounded-full dark:bg-{{ $status->color() }}-900/50 dark:text-{{ $status->color() }}-300 inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $status->color() }}-500 animate-pulse"></span>
                            {{ $status->label() }}
                        </span>
                        <p class="mt-2.5 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $status->description() }}</p>
                    </div>

                    @if($status->value === \Paparee\Rakaca\Enums\SubmissionStatus::MenungguBerkas->value)
                        <x-core::button link href="{{ route('rakaca.guest.submission.edit', $submission->id) }}" label="{{ __('Lengkapi Berkas') }}" class="w-full justify-center">
                            <x-slot name="icon"><x-lucide-upload class="w-4 h-4" /></x-slot>
                        </x-core::button>
                    @endif

                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 p-3.5 flex items-start gap-2.5">
                        <x-lucide-info class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Terakhir diubah') }}: {{ optional($submission->status_changed_at)->diffForHumans() ?? $submission->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-2.5">
                        <x-core::secondary-button link href="{{ route('rakaca.guest.submission.index') }}" label="{{ __('Kembali') }}" class="w-full justify-center" />
                        @if($status->cancellableByUser())
                            <x-core::danger-button type="button"
                                wire:click="cancelSubmission('{{ $submission->id }}')"
                                wire:confirm="{{ __('Yakin ingin membatalkan tiket ini?') }}"
                                label="{{ __('Batalkan Tiket') }}"
                                class="w-full justify-center">
                                <x-slot name="icon"><x-lucide-x class="w-4 h-4" /></x-slot>
                            </x-core::danger-button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>