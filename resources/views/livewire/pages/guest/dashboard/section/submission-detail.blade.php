@php
    $serviceSlug = $submission?->service?->slug ?? 'default';
    $icon = $this->getServiceIcon($serviceSlug);
    $color = $this->getServiceColor($serviceSlug);
    $statusColor = $submission?->statusColor ?? 'gray';
@endphp

{{-- Detail Modal --}}
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show"
    x-init="$watch('show', value => { if(!value) $wire.call('closeModal') })"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
        @click="show = false"></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl p-6"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.stop>

            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-{{ $color }}-100 dark:bg-{{ $color }}-900/50 rounded-xl">
                        <x-dynamic-component :component="'lucide-' . $icon"
                            class="w-6 h-6 text-{{ $color }}-600 dark:text-{{ $color }}-400" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $submission?->service?->name ?? 'Unknown Service' }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pengajuan #{{ $submission?->code }}
                        </p>
                    </div>
                </div>
                <button @click="show = false"
                    class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Status Badge --}}
            <div class="mb-6">
                <span
                    class="px-4 py-2 text-sm font-semibold text-{{ $statusColor }}-700 bg-{{ $statusColor }}-100 rounded-full dark:bg-{{ $statusColor }}-900/50 dark:text-{{ $statusColor }}-300 inline-flex items-center gap-2">
                    <span class="w-2 h-2 bg-{{ $statusColor }}-500 rounded-full"></span>
                    {{ $submission?->statusLabel }}
                </span>
            </div>

            {{-- Submission Info --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tanggal Pengajuan</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $submission?->created_at?->format('d F Y') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $submission?->created_at?->format('H:i') }} WIB
                    </p>
                </div>

                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Terakhir Diupdate</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $submission?->updated_at?->format('d F Y') }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ $submission?->updated_at?->diffForHumans() }}
                    </p>
                </div>
            </div>

            {{-- Submission Data --}}
            @if($submission?->data)
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Detail Pengajuan</h4>
                    <div class="space-y-2">
                        @foreach($submission->data as $key => $value)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        @if(is_array($value))
                                            {{ json_encode($value, JSON_PRETTY_PRINT) }}
                                        @else
                                            {{ $value }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Service Description --}}
            @if($submission?->service?->description)
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Tentang Layanan</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $submission->service->description }}
                    </p>
                </div>
            @endif

            {{-- Footer Actions --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    ID Pengajuan: {{ $submission?->id }}
                </p>
                <button @click="show = false"
                    class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 rounded-lg transition-all shadow-md hover:shadow-lg">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>