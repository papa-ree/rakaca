<div>
    <x-core::page-header gradient :title="__('Bale List Management')" :subtitle="__('Manage Bale instances and their databases')">
        <x-slot name="action">
            @can('bale-list.create')
                <a href="{{ route('rakaca.landlord.bale-list.create') }}" wire:navigate
                    class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <x-lucide-plus class="w-5 h-5" />
                    {{ __('Add New Bale List') }}
                </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    <x-core::table :links="$this->baleLists" header>
        <x-slot name="thead">
            <tr>
                <x-core::table-th :label="__('Instance Name')" sortBy="name" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th :label="__('Organization')" />
                <x-core::table-th :label="__('Database')" />
                <x-core::table-th :label="__('Status')" sortBy="is_active" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                @canany(['bale-list.update', 'bale-list.delete'])
                    <x-core::table-th :label="__('Action')" />
                @endcanany
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($this->baleLists as $item)
                <tr wire:key='bale-list-{{ $item->id }}'
                    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
                        <a wire:click="selectBale('{{ $item->id }}')" class="flex items-center gap-x-3 cursor-pointer">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2">
                                <x-lucide-server class="w-6 h-6" />
                            </div>
                            <div>
                                <span class="block text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $item->name }}</span>
                                <span class="block text-xs text-gray-500">{{ $item->slug }}</span>
                            </div>
                        </a>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $item->organization->name ?? '-' }}
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <div class="flex flex-col">
                            <span class="text-xs font-mono">{{ $item->database_host }}</span>
                            <span class="text-xs font-semibold">{{ $item->database_name }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        @if($item->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                {{ __('Active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                {{ __('Inactive') }}
                            </span>
                        @endif
                    </td>
                    @canany(['bale-list.update', 'bale-list.delete'])
                        <td class="size-px whitespace-nowrap">
                            <div class="px-6 py-1.5">
                                <livewire:core.shared-components.item-actions
                                    :editUrl="route('rakaca.landlord.bale-list.edit', $item->id)"
                                    :deleteId="$item->id" wire:key="item-actions-{{ $item->id }}"
                                    confirmMessage="{{ __('Are you sure you want to delete this Bale List?') }}" />
                            </div>
                        </td>
                    @endcanany
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-4 text-sm text-gray-500 text-center">
                        {{ __('No Bale Lists found.') }}
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-core::table>
</div>
