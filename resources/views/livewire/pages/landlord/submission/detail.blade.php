<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Submissions'), 'route' => 'rakaca.landlord.submission.index'],
    ]" :active="__('Detail Submission')" />

    <div class="mt-6 max-w-5xl mx-auto">
        <x-core::page-header :title="__('Detail Pengajuan')" :subtitle="__('Kode: ').$submission->code">
            <x-slot name="actions">
                <x-core::button type="button" variant="secondary" wire:click="back" :label="__('Kembali')">
                    <x-slot name="icon"><x-lucide-arrow-left class="w-4 h-4" /></x-slot>
                </x-core::button>
            </x-slot>
        </x-core::page-header>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            {{-- Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Status & Layanan --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-{{ $submission->statusColor }}-50 text-{{ $submission->statusColor }}-700 ring-1 ring-{{ $submission->statusColor }}-600/20 dark:bg-{{ $submission->statusColor }}-900/30 dark:text-{{ $submission->statusColor }}-300">
                            {{ __($submission->statusLabel) }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $submission->created_at->format('d M Y H:i') }} WIB</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Nama Pemohon') }}</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $submission->user?->name ?? $submission->items['data']['nama_lengkap'] ?? $submission->items['data']['nama'] ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $submission->user?->email ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('NIP') }}</p>
                            @php
                                $nipRaw = $submission->items['data']['nip'] ?? $submission->items['data']['NIP'] ?? '-';
                                try { $nip = \Illuminate\Support\Facades\Crypt::decryptString($nipRaw); } catch (\Throwable $e) { $nip = $nipRaw; }
                                $nip = \Bale\Core\Support\Sanitize::text((string) $nip);
                            @endphp
                            <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $nip }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Layanan') }}</p>
                            <p class="font-medium text-indigo-600 dark:text-indigo-400">{{ $submission->form?->service?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $submission->form?->name ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Kode') }}</p>
                            <p class="font-mono text-sm text-gray-900 dark:text-white">{{ $submission->code }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Status berubah') }}: {{ optional($submission->status_changed_at)->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Data --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-list class="w-4 h-4 text-indigo-600 dark:text-indigo-400" /> {{ __('Data Pengajuan') }}
                    </h3>
                    @if(!empty($submission->items['data']))
                        <div class="space-y-3">
                            @foreach($submission->items['data'] as $key => $value)
                                @php
                                    $val = is_string($value) ? \Bale\Core\Support\Sanitize::text($value) : $value;
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-3 p-3 bg-gray-50 dark:bg-slate-800/50 rounded-xl">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 min-w-[10rem]">{{ ucwords(str_replace('_',' ', $key)) }}</span>
                                    <span class="text-sm text-gray-900 dark:text-white break-all">
                                        @if(is_array($val))
                                            {{ json_encode($val) }}
                                        @elseif(Str::startsWith($val, 'rakaca-submissions/') || Str::startsWith($val, 'rakaca-submission-uploads/'))
                                            <a href="{{ Storage::disk('public')->url($val) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ basename($val) }}</a>
                                        @else
                                            {{ $val }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Tidak ada data.') }}</p>
                    @endif
                </div>

                {{-- Uploads Preview Multi --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-file-text class="w-4 h-4 text-amber-600 dark:text-amber-400" /> {{ __('Berkas Pendukung') }} (PDF, max 3)
                    </h3>
                    @if($submission->uploads->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada berkas.') }}</p>
                    @else
                        <div class="grid gap-3">
                            @foreach($submission->uploads as $up)
                                <div class="flex items-center justify-between p-3 bg-amber-50/50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-800/20">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $up->original_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($up->size/1024, 1) }} KB · {{ $up->mime_type }} · {{ $up->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <a href="{{ Storage::disk('public')->url($up->file_path) }}" target="_blank" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-800/40 rounded-lg hover:bg-indigo-50 dark:hover:bg-slate-700">
                                        <x-lucide-eye class="w-3 h-3" /> {{ __('Preview') }}
                                    </a>
                                </div>
                                @if(Str::endsWith($up->file_path, '.pdf'))
                                    <iframe src="{{ Storage::disk('public')->url($up->file_path) }}" class="w-full h-[400px] rounded-lg border border-gray-200 dark:border-slate-800" loading="lazy"></iframe>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Riwayat Respon --}}
                @if($submission->response && ($submission->response->rejection_reason || $submission->response->revise_note || $submission->response->resolution_data))
                    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <x-lucide-history class="w-4 h-4 text-purple-600 dark:text-purple-400" /> {{ __('Riwayat Respon') }}
                        </h3>
                        <div class="space-y-3">
                            @if($submission->response->rejection_reason)
                                <div class="p-3 bg-red-50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-800/20">
                                    <p class="text-xs font-semibold text-red-700 dark:text-red-400">{{ __('Alasan Ditolak') }}</p>
                                    <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $submission->response->rejection_reason }}</p>
                                </div>
                            @endif
                            @if($submission->response->revise_note)
                                <div class="p-3 bg-orange-50 dark:bg-orange-900/10 rounded-xl border border-orange-100 dark:border-orange-800/20">
                                    <p class="text-xs font-semibold text-orange-700 dark:text-orange-400">{{ __('Catatan Revisi') }}</p>
                                    <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $submission->response->revise_note }}</p>
                                </div>
                            @endif
                            @if(!empty($submission->response->resolution_data))
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/10 rounded-xl border border-emerald-100 dark:border-emerald-800/20">
                                    <p class="text-xs font-semibold text-emerald-700 dark:text-emerald-400 mb-2">{{ __('Hasil Layanan') }}</p>
                                    <dl class="space-y-1.5">
                                        @foreach($submission->response->resolution_data as $rk => $rv)
                                            <div class="flex gap-2 text-sm">
                                                <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 min-w-[10rem]">{{ Str::headline($rk) }}</dt>
                                                <dd class="text-sm text-gray-900 dark:text-white break-all">{{ is_array($rv) ? json_encode($rv) : $rv }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                    @if($submission->response->resolvedBy)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Diproses oleh {{ $submission->response->resolvedBy?->name ?? $submission->response->resolved_by }} · {{ optional($submission->response->resolved_at)->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Ticket Action Panel --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6 sticky top-6 space-y-4"
                    x-data="{ selectedAction: @entangle('selectedAction') }">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Aksi Tiket') }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Status saat ini:') }} <span class="font-semibold text-gray-900 dark:text-white">{{ $submission->statusLabel }}</span></p>

                    @can('submission.update')
                        @if($this->canTakeOver())
                            <x-core::button type="button" variant="primary" wire:click="takeOver" wire:confirm="{{ __('Ambil alih tiket ini (diproses)?') }}" :label="__('Ambil Alih & Proses')" class="w-full justify-center">
                                <x-slot name="icon"><x-lucide-play class="w-4 h-4" /></x-slot>
                            </x-core::button>
                        @else
                            {{-- Pilih Aksi (Instant Alpine.js) --}}
                            <div class="space-y-2 pt-2 border-t border-gray-100 dark:border-slate-800">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Pilih Aksi:') }}</p>
                                <x-core::input-error for="selectedAction" />
                                <div class="grid grid-cols-1 gap-2">
                                    @if($this->canComplete())
                                        <button type="button" @click="selectedAction = (selectedAction === 'complete' ? '' : 'complete')"
                                            class="flex items-center justify-between p-3 rounded-xl border text-xs font-semibold transition-all duration-200 text-left cursor-pointer"
                                            :class="selectedAction === 'complete' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 ring-2 ring-emerald-500/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:border-emerald-300'">
                                            <span class="flex items-center gap-2">
                                                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                                                {{ __('Selesaikan Tiket') }}
                                            </span>
                                            <x-lucide-check class="w-4 h-4 text-emerald-600" x-show="selectedAction === 'complete'" x-cloak />
                                        </button>
                                    @endif

                                    @if($this->canRequestRevision())
                                        <button type="button" @click="selectedAction = (selectedAction === 'revise' ? '' : 'revise')"
                                            class="flex items-center justify-between p-3 rounded-xl border text-xs font-semibold transition-all duration-200 text-left cursor-pointer"
                                            :class="selectedAction === 'revise' ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 ring-2 ring-amber-500/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:border-amber-300'">
                                            <span class="flex items-center gap-2">
                                                <x-lucide-refresh-ccw class="w-4 h-4 text-amber-600" />
                                                {{ __('Minta Revisi') }}
                                            </span>
                                            <x-lucide-check class="w-4 h-4 text-amber-600" x-show="selectedAction === 'revise'" x-cloak />
                                        </button>
                                    @endif

                                    @if($this->canReject())
                                        <button type="button" @click="selectedAction = (selectedAction === 'reject' ? '' : 'reject')"
                                            class="flex items-center justify-between p-3 rounded-xl border text-xs font-semibold transition-all duration-200 text-left cursor-pointer"
                                            :class="selectedAction === 'reject' ? 'border-red-500 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 ring-2 ring-red-500/20' : 'border-gray-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-700 dark:text-gray-300 hover:border-red-300'">
                                            <span class="flex items-center gap-2">
                                                <x-lucide-x class="w-4 h-4 text-red-600" />
                                                {{ __('Tolak Tiket') }}
                                            </span>
                                            <x-lucide-check class="w-4 h-4 text-red-600" x-show="selectedAction === 'reject'" x-cloak />
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Form Input Berdasarkan Aksi Aktif (Alpine x-show) --}}
                            @if($this->canComplete())
                                <div x-show="selectedAction === 'complete'" x-collapse x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800 space-y-3">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Isi Hasil Layanan') }}</p>
                                    @include('rakaca::livewire.pages.landlord.submission.section._resolution-fields', [
                                        'schema' => $submission->form?->response_form_schema ?? [],
                                    ])

                                    <x-core::button type="button" variant="success" wire:click="executeAction" wire:confirm="{{ __('Konfirmasi selesaikan tiket ini?') }}" :label="__('Simpan & Selesaikan')" class="w-full justify-center">
                                        <x-slot name="icon"><x-lucide-check-circle class="w-4 h-4" /></x-slot>
                                    </x-core::button>
                                </div>
                            @endif

                            @if($this->canRequestRevision())
                                <div x-show="selectedAction === 'revise'" x-collapse x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800 space-y-3">
                                    <x-core::label for="revise_note" class="text-xs font-semibold">
                                        {{ __('Catatan Revisi') }} <span class="text-red-500">*</span>
                                    </x-core::label>
                                    <x-core::textarea wire:model="revise_note" id="revise_note" rows="4" :placeholder="__('Tulis catatan revisi untuk pemohon...')" />
                                    <x-core::input-error for="revise_note" />

                                    <x-core::button type="button" variant="primary" wire:click="executeAction" wire:confirm="{{ __('Kirim permintaan revisi?') }}" :label="__('Kirim Revisi')" class="w-full justify-center">
                                        <x-slot name="icon"><x-lucide-send class="w-4 h-4" /></x-slot>
                                    </x-core::button>
                                </div>
                            @endif

                            @if($this->canReject())
                                <div x-show="selectedAction === 'reject'" x-collapse x-cloak class="pt-3 border-t border-gray-100 dark:border-slate-800 space-y-3">
                                    <x-core::label for="reason" class="text-xs font-semibold">
                                        {{ __('Alasan Penolakan') }} <span class="text-red-500">*</span>
                                    </x-core::label>
                                    <x-core::textarea wire:model="reason" id="reason" rows="4" :placeholder="__('Tulis alasan penolakan...')" />
                                    <x-core::input-error for="reason" />

                                    <x-core::button type="button" variant="danger" wire:click="executeAction" wire:confirm="{{ __('Tolak tiket ini?') }}" :label="__('Konfirmasi Penolakan')" class="w-full justify-center">
                                        <x-slot name="icon"><x-lucide-x-circle class="w-4 h-4" /></x-slot>
                                    </x-core::button>
                                </div>
                            @endif
                        @endif
                    @endcan

                    <x-core::button type="button" variant="secondary" wire:click="back" :label="__('Kembali')" class="w-full justify-center">
                        <x-slot name="icon"><x-lucide-arrow-left class="w-4 h-4" /></x-slot>
                    </x-core::button>
                </div>
            </div>
        </div>
    </div>
</div>