<div>
    <x-core::page-header gradient title="Service Management" subtitle="Kelola layanan Rakaca">
        <x-slot name="action">
            @can('service.create')
            <a href="{{ route('rakaca.landlord.service.create') }}" wire:navigate
                class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                <x-lucide-plus class="w-5 h-5" />
                Add New Service
            </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    <x-core::table :links="$this->services" header>
        <x-slot name="thead">
            <tr>
                <x-core::table-th label="Service Name" sortBy="name" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th label="Slug" sortBy="slug" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th label="Status" />
                @can('service.delete')
                    <x-core::table-th label="Action" />
                @endcan
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($this->services as $service)
                <tr wire:key='service-{{ $service->id }}'
                    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 max-w-0 sm:w-auto sm:max-w-none">
                        <div class="flex items-center gap-x-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 font-semibold p-2">
                                @if($service->icon)
                                    <x-dynamic-component :component="'lucide-' . $service->icon" class="w-6 h-6" />
                                @else
                                    <x-lucide-layers class="w-6 h-6" />
                                @endif
                            </div>
                            <span class="block text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $service->name }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-600 dark:text-slate-400">
                            {{ $service->slug }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        @if($service->actived)
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-500">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                                </span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-500">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    @canany(['service.update', 'service.delete'])
                        <td class="size-px whitespace-nowrap">
                            <div class="px-6 py-1.5">
                                <livewire:core.shared-components.item-actions
                                    :editUrl="route('rakaca.landlord.service.edit', $service->id)" 
                                    :deleteId="$service->id"
                                    wire:key="item-actions-{{ $service->id }}"
                                    confirmMessage="Are you sure you want to delete this service?" />
                            </div>
                        </td>
                    @endcanany
                </tr>
            @endforeach
        </x-slot>
    </x-core::table>
</div>
