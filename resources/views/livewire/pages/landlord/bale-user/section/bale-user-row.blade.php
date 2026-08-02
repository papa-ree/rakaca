<tr wire:key="user-row-{{ $record->uuid }}"
    class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors duration-150">
    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 shrink-0">
                <x-lucide-user class="w-4 h-4" />
            </div>
            <div class="min-w-0">
                <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $record->name }}</span>
                <span class="block text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ $record->email }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 text-sm text-gray-500">
        <div class="flex flex-wrap gap-1.5">
            @forelse($record->baleUsers as $assignment)
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $assignment->bale?->name ?? '-' }}</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                        {{ $assignment->role === 'root' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' :
                           ($assignment->role === 'admin' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                           'bg-gray-200 text-gray-600 dark:bg-gray-700 dark:text-gray-400') }}">
                        {{ $assignment->role }}
                    </span>
                    @canany(['bale-user.update', 'bale-user.delete'])
                        @if (!$record->hasRole('root') || auth()->user()->hasRole('root'))
                            <div class="flex items-center gap-0.5 ms-0.5">
                                <a href="{{ route('rakaca.landlord.bale-user.edit', $assignment->id) }}" wire:navigate.hover
                                    class="p-0.5 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 rounded transition-colors" title="{{ __('Edit') }}">
                                    <x-lucide-pencil class="w-3 h-3" />
                                </a>
                                <livewire:core.shared-components.item-actions
                                    :deleteId="$assignment->id"
                                    :navigate="false"
                                    :confirmMessage="__('Remove this bale assignment?')"
                                    wire:key="item-actions-{{ $assignment->id }}" />
                            </div>
                        @endif
                    @endcanany
                </div>
            @empty
                <span class="text-xs text-gray-400 dark:text-gray-500 italic">{{ __('No bale assignments') }}</span>
            @endforelse
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap w-px text-right">
        @can('bale-user.create')
            <a href="{{ route('rakaca.landlord.bale-user.create', ['user' => $record->uuid]) }}" wire:navigate.hover
                class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:text-indigo-300 dark:hover:bg-indigo-900/20 rounded-lg transition-colors">
                <x-lucide-plus class="w-3 h-3" />
                {{ __('Add') }}
            </a>
        @endcan
    </td>
</tr>
