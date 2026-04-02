<tr wire:key="bale-list-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
        <button wire:click="selectBale('{{ $record->id }}')" class="flex items-center gap-x-3 text-left w-full group">
            <div
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2 shadow-sm ring-1 ring-inset ring-indigo-500/10 group-hover:scale-110 transition-transform duration-200">
                <x-lucide-server class="w-5 h-5" />
            </div>
            <div>
                <span class="block text-sm text-gray-800 dark:text-gray-200 font-bold group-hover:text-indigo-600 dark:group-hover:text-indigo-400 decoration-indigo-500/30 underline-offset-4 group-hover:underline transition-colors">{{ $record->name }}</span>
                <span class="block text-[10px] text-gray-400 dark:text-gray-500 font-mono">{{ $record->slug }}</span>
            </div>
        </button>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        @if($record->organization)
            <div class="flex items-center gap-x-2">
                <x-lucide-building-2 class="w-4 h-4 text-gray-400" />
                <span class="text-gray-700 dark:text-gray-300">{{ $record->organization->name }}</span>
            </div>
        @else
            <span class="text-gray-400 dark:text-gray-600">-</span>
        @endif
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <div class="flex flex-col">
            <span class="text-[10px] font-mono text-gray-400 dark:text-gray-500">{{ $record->database_host }}</span>
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $record->database_name }}</span>
        </div>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        @if($record->is_active)
            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-900/30 dark:text-green-400">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                {{ __('Active') }}
            </span>
        @else
            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-900/30 dark:text-red-400">
                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                {{ __('Inactive') }}
            </span>
        @endif
    </td>
    @canany(['bale-list.update', 'bale-list.delete'])
        <td class="px-6 py-1.5 whitespace-nowrap w-px">
            <livewire:core.shared-components.item-actions
                :editUrl="route('rakaca.landlord.bale-list.edit', $record->id)"
                :deleteId="$record->id" 
                :navigate="false"
                wire:key="item-actions-{{ $record->id }}"
                confirmMessage="{{ __('Are you sure you want to delete this Bale List?') }}" />
        </td>
    @endcanany
</tr>
