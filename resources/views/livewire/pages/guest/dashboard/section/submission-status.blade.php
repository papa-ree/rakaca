<div>
    <div id="submissions" class="mb-8" data-aos="fade-up" data-aos-delay="200">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                        <x-lucide-clipboard-list class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status & History Pengajuan</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Pantau status dan riwayat pengajuan layanan Anda</p>
                    </div>
                </div>
                <a href="{{ route('rakaca.guest.submission.index') }}" wire:navigate class="inline-flex items-center gap-1 text-sm font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                    {{ __('Lihat semua') }}
                    <x-lucide-arrow-right class="w-4 h-4" />
                </a>
            </div>

            @if($this->submissions->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <div class="flex items-center justify-center w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700">
                        <x-lucide-inbox class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Pengajuan</h4>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        Anda belum memiliki pengajuan layanan. Mulai dengan memilih layanan yang Anda butuhkan.
                    </p>
                    <a href="#services">
                        <x-core::button label="Mulai Pengajuan" type="button" class="inline-flex items-center gap-x-2">
                            <x-slot name="icon">
                                <x-lucide-plus class="w-5 h-5" />
                            </x-slot>
                        </x-core::button>
                    </a>
                </div>
            @else
                {{-- Submissions List --}}
                <div class="space-y-3">
                    @foreach($this->submissions as $submission)
                        @php
                            $service = $submission->form?->service;
                            $serviceName = $service?->name ?? 'Layanan TIK';
                            $serviceIcon = $service?->icon ?? 'layers';
                            $formName = $submission->form?->name;
                            $statusColor = $submission->statusColor;
                        @endphp
                        <div class="group flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50/70 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700/80 rounded-xl hover:bg-gray-100/80 dark:hover:bg-gray-700/70 hover:shadow-sm transition-all duration-200 gap-4">
                            <a href="{{ route('rakaca.guest.submission.show', $submission->id) }}" wire:navigate class="flex items-start sm:items-center gap-3 min-w-0 flex-1">
                                <div class="p-1.5 bg-linear-to-br from-purple-500 to-purple-600 rounded-lg shadow-md shrink-0 group-hover:scale-105 transition-transform duration-200">
                                    <x-dynamic-component :component="'lucide-' . $serviceIcon" class="w-4 h-4 text-white" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="font-bold text-gray-900 dark:text-white text-sm sm:text-base group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                            {{ $serviceName }}
                                        </h4>
                                        @if($formName)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">· {{ $formName }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-1 flex-wrap">
                                        <span class="font-mono text-gray-700 dark:text-gray-300 font-medium">#{{ $submission->code }}</span>
                                        <span>·</span>
                                        <span>{{ $submission->created_at->format('d M Y H:i') }}</span>
                                        @if($submission->uploads->count() > 0)
                                            <span>·</span>
                                            <span class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-300">
                                                <x-lucide-paperclip class="w-3 h-3" />
                                                {{ $submission->uploads->count() }} berkas
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>

                            <div class="flex items-center justify-between sm:justify-end gap-3 border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-200 dark:border-gray-700 shrink-0">
                                <span class="px-3 py-1 text-xs font-semibold text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100 rounded-full dark:bg-{{ $statusColor }}-900/50 dark:text-{{ $statusColor }}-300 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $statusColor }}-500 animate-pulse"></span>
                                    {{ $submission->statusLabel }}
                                </span>
                                <a href="{{ route('rakaca.guest.submission.show', $submission->id) }}" wire:navigate class="inline-flex items-center gap-1 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 hover:underline">
                                    {{ __('Detail') }}
                                    <x-lucide-chevron-right class="w-3.5 h-3.5" />
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>