<tr wire:key="submission-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
    <td class="px-3 py-4 text-sm text-gray-500">
        <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800/50 px-2 py-1 rounded text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
            {{ $record->code }}
        </span>
    </td>
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
        <span class="block text-sm text-gray-800 dark:text-gray-200 font-bold truncate">
            {{ $record->service?->name ?? '-' }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-{{ $record->status_color }}-50 text-{{ $record->status_color }}-700 ring-1 ring-inset ring-{{ $record->status_color }}-600/20 dark:bg-{{ $record->status_color }}-900/30 dark:text-{{ $record->status_color }}-400">
            <span class="h-2 w-2 rounded-full bg-{{ $record->status_color }}-500"></span>
            {{ __($record->status_label) }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        {{ $record->created_at }}
    </td>
    @canany(['submission.update', 'submission.delete'])
        <td class="px-6 py-1.5 whitespace-nowrap w-px">
            <livewire:core.shared-components.item-actions
                :editUrl="route('rakaca.landlord.submission.edit', $record->id)" 
                :deleteId="$record->id"
                :navigate="false"
                wire:key="item-actions-{{ $record->id }}"
                :confirmMessage="__('Are you sure you want to delete this submission?')" />
        </td>
    @endcanany
</tr>
