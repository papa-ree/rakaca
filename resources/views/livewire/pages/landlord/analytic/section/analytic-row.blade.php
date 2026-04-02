<tr wire:key="analytic-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
    <td class="px-3 py-4 text-sm text-gray-800 dark:text-gray-200">
        {{ $record->bale_id }}
    </td>
    <td class="px-3 py-4 text-sm text-gray-500 uppercase">
        {{ $record->provider }}
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        {{ $record->website_id }}
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        {{ $record->domain ?? '-' }}
    </td>
    <td class="px-3 py-4 text-sm">
        @if($record->enabled)
            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Enabled') }}</span>
        @else
            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">{{ __('Disabled') }}</span>
        @endif
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        {{ $record->created_at }}
    </td>
    @canany(['analytic.update', 'analytic.delete'])
        <td class="px-6 py-1.5 whitespace-nowrap w-px">
            <livewire:core.shared-components.item-actions 
                :editUrl="route('rakaca.landlord.analytic.edit', $record->id)"
                :deleteId="$record->id" 
                :navigate="false"
                wire:key="item-actions-{{ $record->id }}"
                confirmMessage="{{ __('Are you sure you want to delete this analytic?') }}" />
        </td>
    @endcanany
</tr>
