<tr wire:key="organization-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
        <div class="flex items-center gap-x-3">
            <div
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2 shadow-sm ring-1 ring-inset ring-indigo-500/10">
                <x-lucide-building-2 class="w-5 h-5" />
            </div>
            <span
                class="block text-sm text-gray-800 dark:text-gray-200 font-bold truncate">{{ $record->name }}</span>
        </div>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <span
            class="text-xs font-mono bg-slate-100 dark:bg-slate-800/50 px-2 py-1 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
            {{ $record->slug }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        {{ $record->created_at }}
    </td>
    @canany(['organization.update', 'organization.delete'])
        <td class="px-6 py-1.5 whitespace-nowrap w-px">
            <livewire:core.shared-components.item-actions
                :editUrl="route('rakaca.landlord.organization.edit', $record->id)"
                :deleteId="$record->id" 
                :navigate="false"
                wire:key="organization-actions-{{ $record->id }}"
                confirmMessage="{{ __('Are you sure you want to delete this organization?') }}" />
        </td>
    @endcanany
</tr>
