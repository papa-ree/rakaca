<div>
    <x-core::page-header gradient :title="__('Bale User Management')" :subtitle="__('Manage user assignments to Bale instances')">
        <x-slot name="action">
            @can('bale-user.create')
                <a href="{{ route('rakaca.landlord.bale-user.create') }}" wire:navigate
                    class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <x-lucide-plus class="w-5 h-5" />
                    {{ __('Add New Bale User') }}
                </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    <x-core::table :links="$this->baleUsers" header>
        <x-slot name="thead">
            <tr>
                <x-core::table-th :label="__('User')" />
                <x-core::table-th :label="__('Bale Instance')" />
                <x-core::table-th :label="__('Role')" />
                <x-core::table-th :label="__('Assigned At')" sortBy="created_at" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                @canany(['bale-user.update', 'bale-user.delete'])
                    <x-core::table-th :label="__('Action')" />
                @endcanany
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($this->baleUsers as $item)
                <tr wire:key='bale-user-{{ $item->id }}'
                    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
                        <div class="flex items-center gap-x-3">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2">
                                <x-lucide-user class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="block text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $item->user?->name ?? '-' }}</span>
                                <span class="block text-xs text-gray-500">{{ $item->user?->email ?? '-' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $item->bale?->name ?? '-' }}</span>
                            <span class="text-xs text-gray-500">{{ $item->bale?->slug ?? '' }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $item->role === 'root' ? 'bg-purple-100 text-purple-700' : ($item->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($item->role) }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $item->created_at?->format('d M Y') }}
                    </td>
                    @canany(['bale-user.update', 'bale-user.delete'])
                        <td class="size-px whitespace-nowrap">
                            @if (!$item->user?->hasRole('root') || auth()->user()->hasRole('root'))
                                <div class="px-6 py-1.5">
                                    <livewire:core.shared-components.item-actions
                                        :editUrl="route('rakaca.landlord.bale-user.edit', $item->id)"
                                        :deleteId="$item->id" wire:key="item-actions-{{ $item->id }}"
                                        confirmMessage="{{ __('Are you sure you want to delete this Bale User?') }}" />
                                </div>
                            @endif
                        </td>
                    @endcanany
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">
                        {{ __('No Bale Users found.') }}
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-core::table>
</div>
