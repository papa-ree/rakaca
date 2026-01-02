<div>
    <div id="submissions" class="mb-8" data-aos="fade-up" data-aos-delay="200">
        <div class="p-6 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status Pengajuan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau status pengajuan layanan Anda</p>
                </div>
                <div class="p-3 bg-linear-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg">
                    <x-lucide-clipboard-list class="w-6 h-6 text-white" />
                </div>
            </div>

            @if($this->submissions->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <div
                        class="flex items-center justify-center w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700">
                        <x-lucide-inbox class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Pengajuan</h4>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
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
                            $serviceIcon = $submission->service->icon ?? 'default';
                            $icon = $this->getServiceIcon($serviceIcon);
                            $color = $this->getServiceColor($serviceIcon);
                            $statusColor = $submission->statusColor;
                        @endphp
                        <div
                            class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/50 rounded-lg">
                                        <x-dynamic-component :component="'lucide-' . $icon"
                                            class="w-5 h-5 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">
                                            {{ $submission->service->name ?? 'Unknown Service' }}
                                        </h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Pengajuan #{{ $submission->code }}
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="px-3 py-1 text-xs font-semibold text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100 rounded-full dark:bg-{{ $statusColor }}-900/50 dark:text-{{ $statusColor }}-300">
                                    {{ $submission->statusLabel }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">
                                    Diajukan: {{ $submission->created_at->format('d M Y') }}
                                </span>
                                <button wire:click="viewDetail('{{ $submission->id }}')"
                                    class="text-blue-600 dark:text-blue-400 hover:underline">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetail && $selectedSubmissionId)
        <livewire:rakaca.pages.guest.dashboard.section.submission-detail :submissionId="$selectedSubmissionId"
            wire:key="detail-{{ $selectedSubmissionId }}" />
    @endif
</div>