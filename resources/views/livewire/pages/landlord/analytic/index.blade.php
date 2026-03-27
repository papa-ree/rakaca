<div>
    <x-core::page-header gradient :title="__('Analytic Management')" :subtitle="__('Manage analytic integration for tenant instances')">
        <x-slot name="action">
            @can('analytic.create')
                <a href="{{ route('rakaca.landlord.analytic.create') }}" wire:navigate
                    class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <x-lucide-plus class="w-5 h-5" />
                    {{ __('Add New Analytic') }}
                </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    <x-core::table :links="$this->analytics" header>
        <x-slot name="thead">
            <tr>
                <x-core::table-th :label="__('Bale Instance')" />
                <x-core::table-th :label="__('Provider')" />
                <x-core::table-th :label="__('Website ID')" />
                <x-core::table-th :label="__('Domain')" />
                <x-core::table-th :label="__('Status')" />
                <x-core::table-th :label="__('Created At')" sortBy="created_at" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                @canany(['analytic.update', 'analytic.delete'])
                    <x-core::table-th :label="__('Action')" />
                @endcanany
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @forelse ($this->analytics as $item)
                <tr wire:key='analytic-{{ $item->id }}'
                    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <td class="px-3 py-4 text-sm text-gray-800 dark:text-gray-200">
                        {{ $item->bale_id }}
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500 uppercase">
                        {{ $item->provider }}
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $item->website_id }}
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $item->domain ?? '-' }}
                    </td>
                    <td class="px-3 py-4 text-sm">
                        @if($item->enabled)
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">{{ __('Enabled') }}</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">{{ __('Disabled') }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $item->created_at?->format('d M Y') }}
                    </td>
                    @canany(['analytic.update', 'analytic.delete'])
                        <td class="size-px whitespace-nowrap">
                            <div class="px-6 py-1.5">
                                <livewire:core.shared-components.item-actions
                                    :editUrl="route('rakaca.landlord.analytic.edit', $item->id)"
                                    :deleteId="$item->id" wire:key="item-actions-{{ $item->id }}"
                                    confirmMessage="{{ __('Are you sure you want to delete this analytic?') }}" />
                            </div>
                        </td>
                    @endcanany
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-4 text-sm text-gray-500 text-center">
                        {{ __('No analytics found.') }}
                    </td>
                </tr>
            @endforelse
        </x-slot>
    </x-core::table>
</div>
