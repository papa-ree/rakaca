<div>
    <x-core::page-header gradient title="Pengajuan Saya" subtitle="Daftar pengajuan layanan TI yang telah dibuat">
        <x-slot name="action">
            <x-core::button link href="{{ route('rakaca.guest.submission.create') }}" label="Buat Pengajuan Baru">
                <x-slot name="icon">
                    <x-lucide-plus class="w-5 h-5" />
                </x-slot>
            </x-core::button>
        </x-slot>
    </x-core::page-header>

    <div class="mt-6 space-y-4">
        @forelse ($this->submissions as $submission)
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                <x-lucide-file-text class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $submission->form?->name ?? 'Unknown Form' }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-mono text-xs bg-slate-100 dark:bg-slate-800/50 px-2 py-0.5 rounded">{{ $submission->code }}</span>
                                    &middot;
                                    {{ $submission->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($submission->status === 'pending')
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-400">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    Menunggu
                                </span>
                            @elseif ($submission->status === 'approved')
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-50 text-teal-700 ring-1 ring-inset ring-teal-600/20 dark:bg-teal-900/30 dark:text-teal-400">
                                    Disetujui
                                </span>
                            @elseif ($submission->status === 'rejected')
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-400">
                                    Ditolak
                                </span>
                            @elseif ($submission->status === 'review')
                                <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20 dark:bg-blue-900/30 dark:text-blue-400">
                                    Dalam Review
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Form Data Summary --}}
                    @if ($submission->items && isset($submission->items['data']))
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-800">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($submission->items['data'] as $key => $value)
                                    @if (!empty($value))
                                        <div>
                                            <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                            <p class="text-sm text-gray-900 dark:text-white">{{ is_bool($value) ? ($value ? 'Ya' : 'Tidak') : $value }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    @if ($submission->status === 'pending')
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-slate-800 flex items-center justify-end gap-2">
                            <x-core::button link href="{{ route('rakaca.guest.submission.edit', $submission->id) }}" label="Edit" variant="secondary">
                                <x-slot name="icon">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </slot>
                            </x-core::button>
                            <x-core::danger-button label="Hapus" wire:click="deleteSubmission('{{ $submission->id }}')" />
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl p-12 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-gray-100 dark:bg-slate-800 rounded-full mb-4">
                    <x-lucide-inbox class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belum ada pengajuan</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Mulai buat pengajuan layanan TI pertama Anda.</p>
                <x-core::button link href="{{ route('rakaca.guest.submission.create') }}" label="Buat Pengajuan">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            </div>
        @endforelse
    </div>
</div>
