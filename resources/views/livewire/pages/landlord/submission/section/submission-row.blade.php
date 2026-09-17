@php
    $nama = $record->user?->name ?? ($record->items['data']['nama_lengkap'] ?? $record->items['data']['nama'] ?? $record->items['data']['name'] ?? '-');
    $nama = \Bale\Core\Support\Sanitize::text((string) $nama);
    $nipRaw = $record->items['data']['nip'] ?? $record->items['data']['NIP'] ?? $record->items['data']['nip_lengkap'] ?? '-';
    try {
        $nipDecrypted = \Illuminate\Support\Facades\Crypt::decryptString($nipRaw);
        $nip = \Bale\Core\Support\Sanitize::text($nipDecrypted);
    } catch (\Throwable $e) {
        $nip = \Bale\Core\Support\Sanitize::text((string) $nipRaw);
    }
    $layanan = $record->form?->service?->name ?? '-';
@endphp
<tr wire:key="submission-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-normal">
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
        <div class="flex items-center gap-x-3">
            <div
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2 shadow-sm ring-1 ring-inset ring-indigo-500/10">
                <x-lucide-file-text class="w-5 h-5" />
            </div>
            <div class="min-w-0">
                <a href="{{ route('rakaca.landlord.submission.detail', $record->id) }}" wire:navigate
                    class="block text-sm text-gray-800 dark:text-gray-200 font-bold truncate hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">
                    {{ $nama }}
                </a>
                <span
                    class="inline-flex mt-1 text-xs font-mono bg-slate-100 dark:bg-slate-800/50 px-2 py-0.5 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                    {{ $record->code }}
                </span>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[12rem] sm:max-w-[14rem]">
                    {{ $record->user?->email ?? '' }}</p>
            </div>
        </div>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <span
            class="text-xs font-mono bg-slate-100 dark:bg-slate-800/50 px-2 py-1 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
            {{ $nip }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500 max-w-xs">
        <span class="text-sm text-gray-800 dark:text-gray-200 truncate">
            {{ $layanan }}
        </span>
        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $record->form?->name ?? '' }}</p>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <span
            class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-{{ $record->statusColor }}-50 text-{{ $record->statusColor }}-700 ring-1 ring-inset ring-{{ $record->statusColor }}-600/20 dark:bg-{{ $record->statusColor }}-900/30 dark:text-{{ $record->statusColor }}-400">
            <span class="relative flex h-2 w-2">
                @if(in_array($record->status?->value, ['siap-direview', 'diproses']))
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-{{ $record->statusColor }}-400 opacity-75"></span>
                @endif
                <span class="relative inline-flex rounded-full h-2 w-2 bg-{{ $record->statusColor }}-500"></span>
            </span>
            {{ __($record->statusLabel) }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $record->created_at->format('d M Y') }}</span>
        <p class="text-xs text-gray-400">{{ $record->created_at->format('H:i') }} WIB</p>
    </td>
    <td class="px-6 py-1.5 whitespace-nowrap w-px">
        <div class="flex items-center gap-1">
            <a href="{{ route('rakaca.landlord.submission.detail', $record->id) }}" wire:navigate
                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                <x-lucide-eye class="w-3 h-3" /> {{ __('Lihat Detail') }}
            </a>
            @can('submission.delete')
                <livewire:core.shared-components.item-actions :deleteId="$record->id" :navigate="false"
                    wire:key="item-actions-{{ $record->id }}" :confirmMessage="__('Hapus pengajuan ini?')" />
            @endcan
        </div>
    </td>
</tr>