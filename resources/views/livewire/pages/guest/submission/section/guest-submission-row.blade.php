@php
    $status = $record->status instanceof \Paparee\Rakaca\Enums\SubmissionStatus
        ? $record->status
        : \Paparee\Rakaca\Enums\SubmissionStatus::fromLegacy($record->status);
@endphp
<tr wire:key="guest-submission-row-{{ $record->getKey() }}"
    class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors duration-150">

    {{-- Kode + Form (primary, always visible) --}}
    <td class="px-4 py-3.5 w-full max-w-0 sm:max-w-none sm:w-auto">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 shrink-0">
                <x-lucide-file-text class="w-4 h-4" />
            </div>
            <div class="min-w-0">
                <div class="font-mono text-xs bg-slate-100 dark:bg-slate-800/50 px-2 py-0.5 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 inline-block">
                    {{ $record->code }}
                </div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-[14rem] sm:max-w-xs mt-1">
                    {{ $record->form?->name ?? __('Unknown Form') }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ $record->form?->service?->name ?? '-' }}
                </div>
            </div>
        </div>
    </td>

    {{-- Status (responsive) --}}
    <td class="px-4 py-3.5 hidden sm:table-cell">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $status->color() }}-50 text-{{ $status->color() }}-700 ring-1 ring-inset ring-{{ $status->color() }}-600/20 dark:bg-{{ $status->color() }}-900/30 dark:text-{{ $status->color() }}-400">
            <span class="h-2 w-2 rounded-full bg-{{ $status->color() }}-500"></span>
            {{ $status->label() }}
        </span>
    </td>

    {{-- Tanggal --}}
    <td class="px-4 py-3.5 hidden md:table-cell">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            {{ $record->created_at->format('d M Y') }}
        </div>
        <div class="text-xs text-gray-500">
            {{ $record->created_at->format('H:i') }} WIB
        </div>
    </td>

    {{-- Actions --}}
    <td class="px-4 py-3.5 whitespace-nowrap w-px">
        <div class="flex items-center gap-1">
            <a href="{{ route('rakaca.guest.submission.show', $record->id) }}" wire:navigate
                class="p-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 rounded-lg transition">
                <x-lucide-eye class="w-4 h-4" />
            </a>
            @if($status->value === \Paparee\Rakaca\Enums\SubmissionStatus::MenungguBerkas->value)
                <a href="{{ route('rakaca.guest.submission.edit', $record->id) }}" wire:navigate
                    class="p-2 text-gray-600 hover:text-amber-600 hover:bg-amber-50 dark:text-gray-400 dark:hover:text-amber-400 rounded-lg transition">
                    <x-lucide-upload class="w-4 h-4" />
                </a>
            @endif
            @if($status->cancellableByUser())
                <button type="button"
                    wire:click="cancelSubmission('{{ $record->id }}')"
                    wire:confirm="{{ __('Yakin ingin membatalkan tiket ini?') }}"
                    class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-400 rounded-lg transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            @endif
        </div>
    </td>

</tr>