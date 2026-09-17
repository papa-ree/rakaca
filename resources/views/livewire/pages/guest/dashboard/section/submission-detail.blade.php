@php
    $service = $submission?->form?->service;
    $serviceName = $service?->name ?? 'Unknown Service';
    $serviceIcon = $service?->icon ?? 'layers';
    $statusColor = $submission?->statusColor ?? 'gray';
@endphp

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full p-6 border border-gray-100 dark:border-gray-700">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <div class="p-3 bg-linear-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg">
                <x-dynamic-component :component="'lucide-' . $serviceIcon"
                    class="w-6 h-6 text-white" />
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $serviceName }}
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Pengajuan #{{ $submission?->code }}
                </p>
            </div>
        </div>
        <a href="{{ route('rakaca.guest.submission.show', $submission?->id ?? '') }}" wire:navigate
            class="px-4 py-2 text-xs font-semibold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors inline-flex items-center gap-1">
            {{ __('Lihat Selengkapnya') }}
            <x-lucide-arrow-right class="w-3.5 h-3.5" />
        </a>
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
                {{ $submission?->created_at }}
            </p>
        </div>

        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Terakhir Diupdate</p>
            <p class="font-semibold text-gray-900 dark:text-white">
                {{ $submission?->updated_at }}
            </p>
        </div>
    </div>

    {{-- Submission Data --}}
    @if($submission?->items['data'] ?? null)
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Detail Pengajuan</h4>
            <div class="space-y-2">
                @foreach($submission->items['data'] as $key => $value)
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
</div>
