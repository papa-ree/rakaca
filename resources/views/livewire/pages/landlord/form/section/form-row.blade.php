<tr wire:key="form-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-normal">
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
        <div class="flex items-center gap-x-3">
            <div
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2 shadow-sm ring-1 ring-inset ring-indigo-500/10">
                <x-lucide-file-text class="w-5 h-5" />
            </div>
            <span class="block text-sm text-gray-800 dark:text-gray-200 font-bold truncate">{{ $record->name }}</span>
        </div>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <span
            class="text-xs font-mono bg-slate-100 dark:bg-slate-800/50 px-2 py-1 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
            {{ $record->slug }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500 max-w-xs">
        <span class="text-sm text-gray-800 dark:text-gray-200">
            {{ $record->service?->name ?? __('Unknown Service') }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        @if($record->actived)
            <span
                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-50 text-teal-700 ring-1 ring-inset ring-teal-600/20 dark:bg-teal-900/30 dark:text-teal-400">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                </span>
                Active
            </span>
        @else
            <span
                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-400">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                Inactive
            </span>
        @endif
    </td>
    @canany(['form.update', 'form.delete'])
        <td class="px-6 py-1.5 whitespace-nowrap w-px">
            <livewire:core.shared-components.item-actions :editUrl="route('rakaca.landlord.form.edit', $record->id)"
                :deleteId="$record->id" :navigate="true" wire:key="item-actions-{{ $record->id }}"
                confirmMessage="Are you sure you want to delete this form?" />
        </td>
    @endcanany
</tr>