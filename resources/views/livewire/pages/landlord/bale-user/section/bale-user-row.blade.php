<tr wire:key="bale-user-row-{{ $record->id }}"
    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
        <div class="flex items-center gap-x-3">
            <div
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2 shadow-sm ring-1 ring-inset ring-indigo-500/10">
                <x-lucide-user class="w-5 h-5" />
            </div>
            <div>
                <span class="block text-sm text-gray-800 dark:text-gray-200 font-bold truncate">{{ $record->user?->name ?? '-' }}</span>
                <span class="block text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ $record->user?->email ?? '-' }}</span>
            </div>
        </div>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <div class="flex flex-col">
            <span class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $record->bale?->name ?? '-' }}</span>
            <span class="text-[10px] text-gray-400 dark:text-gray-500 truncate">{{ $record->bale?->slug ?? '' }}</span>
        </div>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $record->role === 'root' ? 'bg-purple-50 text-purple-700 ring-1 ring-inset ring-purple-600/20' : ($record->role === 'admin' ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20' : 'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20') }}">
            {{ $record->role }}
        </span>
    </td>
    <td class="px-3 py-4 text-sm text-gray-500">
        {{ $record->created_at }}
    </td>
    @canany(['bale-user.update', 'bale-user.delete'])
        <td class="px-6 py-1.5 whitespace-nowrap w-px">
            @if (!$record->user?->hasRole('root') || auth()->user()->hasRole('root'))
                <livewire:core.shared-components.item-actions
                    :editUrl="route('rakaca.landlord.bale-user.edit', $record->id)"
                    :deleteId="$record->id" 
                    :navigate="false"
                    wire:key="item-actions-{{ $record->id }}"
                    confirmMessage="{{ __('Are you sure you want to delete this Bale User?') }}" />
            @endif
        </td>
    @endcanany
</tr>
