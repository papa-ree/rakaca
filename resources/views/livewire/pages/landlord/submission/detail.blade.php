<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Submissions'), 'route' => 'rakaca.landlord.submission.index'],
    ]" :active="__('Detail Submission')" />

    <div class="mt-6 max-w-5xl mx-auto">
        <x-core::page-header :title="__('Detail Pengajuan')" :subtitle="__('Kode: ').$submission->code">
            <x-slot name="actions">
                <button wire:click="back" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                    <x-lucide-arrow-left class="w-4 h-4" /> {{ __('Kembali') }}
                </button>
            </x-slot>
        </x-core::page-header>

        <div class="mt-6 grid gap-6 lg:grid-cols-3">
            {{-- Main Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Status & Layanan --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-{{ $submission->statusColor }}-50 text-{{ $submission->statusColor }}-700 ring-1 ring-{{ $submission->statusColor }}-600/20">
                            {{ __($submission->statusLabel) }}
                        </span>
                        <span class="text-xs text-gray-500">{{ $submission->created_at->format('d M Y H:i') }} WIB</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Nama Pemohon') }}</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $submission->user?->name ?? $submission->items['data']['nama_lengkap'] ?? $submission->items['data']['nama'] ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->user?->email ?? '' }}</p>
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
                            <p class="text-xs text-gray-500">{{ $submission->form?->name ?? '' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('Kode') }}</p>
                            <p class="font-mono text-sm">{{ $submission->code }}</p>
                        </div>
                    </div>
                </div>

                {{-- Form Data --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-list class="w-4 h-4 text-indigo-600" /> {{ __('Data Pengajuan') }}
                    </h3>
                    @if(!empty($submission->items['data']))
                        <div class="space-y-3">
                            @foreach($submission->items['data'] as $key => $value)
                                @php
                                    // Sanitize display
                                    $val = is_string($value) ? \Bale\Core\Support\Sanitize::text($value) : $value;
                                @endphp
                                <div class="flex flex-col sm:flex-row sm:items-start gap-1 sm:gap-3 p-3 bg-gray-50 dark:bg-slate-800/50 rounded-xl">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 min-w-[10rem]">{{ ucwords(str_replace('_',' ', $key)) }}</span>
                                    <span class="text-sm text-gray-900 dark:text-white break-all">
                                        @if(is_array($val))
                                            {{ json_encode($val) }}
                                        @elseif(Str::startsWith($val, 'rakaca-submissions/') || Str::startsWith($val, 'rakaca-submission-uploads/'))
                                            <a href="{{ Storage::disk('public')->url($val) }}" target="_blank" class="text-indigo-600 hover:underline">{{ basename($val) }}</a>
                                        @else
                                            {{ $val }}
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('Tidak ada data.') }}</p>
                    @endif
                </div>

                {{-- Uploads Preview Multi --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-file-text class="w-4 h-4 text-amber-600" /> {{ __('Berkas Pendukung') }} (PDF, max 3)
                    </h3>
                    @if($submission->uploads->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('Belum ada berkas.') }}</p>
                    @else
                        <div class="grid gap-3">
                            @foreach($submission->uploads as $up)
                                <div class="flex items-center justify-between p-3 bg-amber-50/50 dark:bg-amber-900/10 rounded-xl border border-amber-100 dark:border-amber-800/20">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $up->original_name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($up->size/1024, 1) }} KB · {{ $up->mime_type }} · {{ $up->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <a href="{{ Storage::disk('public')->url($up->file_path) }}" target="_blank" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-indigo-600 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50">
                                        <x-lucide-eye class="w-3 h-3" /> {{ __('Preview') }}
                                    </a>
                                </div>
                                @if(Str::endsWith($up->file_path, '.pdf'))
                                    <iframe src="{{ Storage::disk('public')->url($up->file_path) }}" class="w-full h-[400px] rounded-lg border" loading="lazy"></iframe>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Admin Response --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">{{ __('Respon Admin') }}</h3>
                    <p class="text-xs text-gray-500 mb-3">{{ __('Wajib diisi saat menolak atau menerima. Gunakan textarea.') }}</p>

                    <label for="admin_response" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('Isian Respon') }} <span class="text-red-500">*</span></label>
                    <textarea wire:model="admin_response" id="admin_response" rows="6" placeholder="{{ __('Tulis respon untuk pemohon...') }}" class="block w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-gray-900 dark:text-white p-3 text-sm focus:ring-2 focus:ring-indigo-500"></textarea>
                    @error('admin_response') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

                    @if($submission->admin_response)
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-slate-800 rounded-lg border">
                            <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('Respon Tersimpan') }}</p>
                            <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $submission->admin_response }}</p>
                            @if($submission->processed_at)
                                <p class="text-xs text-gray-500 mt-1">{{ $submission->processed_at->format('d M Y H:i') }} oleh {{ $submission->processedBy?->name ?? $submission->processed_by }}</p>
                            @endif
                        </div>
                    @endif

                    <div class="mt-6 flex flex-col gap-2">
                        @if($submission->status === 'pending')
                            <button wire:click="review" wire:loading.attr="disabled" wire:confirm="{{ __('Ubah status menjadi review?') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl disabled:opacity-50">
                                <x-lucide-eye class="w-4 h-4" /> {{ __('Review') }}
                            </button>
                        @endif
                        @if($submission->status === 'review')
                            <button wire:click="approve" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl disabled:opacity-50">
                                <x-lucide-check class="w-4 h-4" /> {{ __('Diterima') }}
                            </button>
                        @endif
                        @if(in_array($submission->status, ['pending', 'review']))
                            <button wire:click="reject" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl disabled:opacity-50">
                                <x-lucide-x class="w-4 h-4" /> {{ __('Ditolak') }}
                            </button>
                        @endif
                        @if($submission->status !== 'ditutup')
                            <button wire:click="close" wire:loading.attr="disabled" wire:confirm="{{ __('Tutup pengajuan ini? User tidak dapat mengedit lagi.') }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-bold text-white bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-xl disabled:opacity-50">
                                <x-lucide-lock class="w-4 h-4" /> {{ __('Ditutup') }}
                            </button>
                        @endif
                        <button wire:click="back" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl hover:bg-gray-50">
                            <x-lucide-arrow-left class="w-4 h-4" /> {{ __('Kembali') }}
                        </button>
                    </div>

                    <p class="mt-3 text-xs text-gray-500">{{ __('Status saat ini:') }} <span class="font-semibold">{{ $submission->statusLabel }}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>
