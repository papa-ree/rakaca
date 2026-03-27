<div>
    <x-core::page-header gradient :title="__('Organization Management')" :subtitle="__('Manage Bale organizations')">
        <x-slot name="action">
            @can('organization.create')
                <a href="{{ route('rakaca.landlord.organization.create') }}" wire:navigate
                    class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <x-lucide-plus class="w-5 h-5" />
                    {{ __('Add New Organization') }}
                </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    <x-core::table :links="$this->organizations" header>
        <x-slot name="thead">
            <tr>
                <x-core::table-th :label="__('Organization Name')" sortBy="name" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th :label="__('Slug')" sortBy="slug" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th :label="__('Created At')" sortBy="created_at" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                @canany(['organization.update', 'organization.delete'])
                    <x-core::table-th :label="__('Action')" />
                @endcanany
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($this->organizations as $organization)
                <tr wire:key='organization-{{ $organization->id }}'
                    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
                        <div class="flex items-center gap-x-3">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2">
                                <x-lucide-building-2 class="w-6 h-6" />
                            </div>
                            <span
                                class="block text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $organization->name }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <span
                            class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-600 dark:text-slate-400">
                            {{ $organization->slug }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $organization->created_at->format('d M Y, H:i') }}
                    </td>
                    @canany(['organization.update', 'organization.delete'])
                        <td class="size-px whitespace-nowrap">
                            <div class="px-6 py-1.5">
                                <livewire:core.shared-components.item-actions
                                    :editUrl="route('rakaca.landlord.organization.edit', $organization->id)"
                                    :deleteId="$organization->id" wire:key="item-actions-{{ $organization->id }}"
                                    confirmMessage="{{ __('Are you sure you want to delete this organization?') }}" />
                            </div>
                        </td>
                    @endcanany
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-3 py-4 text-sm text-gray-500 text-center">
                        {{ __('No organizations found.') }}
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-core::table>
</div>