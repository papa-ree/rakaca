<div>
    <x-core::page-header gradient :title="__('Submission Management')" :subtitle="__('Manage service submissions')">
        <x-slot name="action">
            @can('submission.create')
                <a href="{{ route('rakaca.landlord.submission.create') }}" wire:navigate
                    class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <x-lucide-plus class="w-5 h-5" />
                    {{ __('Add New Submission') }}
                </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    <x-core::table :links="$this->submissions" header>
        <x-slot name="thead">
            <tr>
                <x-core::table-th :label="__('Code')" sortBy="code" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th :label="__('Service')" />
                <x-core::table-th :label="__('Status')" sortBy="status" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                <x-core::table-th :label="__('Date')" sortBy="created_at" :sortField="$sortField"
                    :sortDirection="$sortDirection" />
                @canany(['submission.update', 'submission.delete'])
                    <x-core::table-th :label="__('Action')" />
                @endcanany
            </tr>
        </x-slot>

        <x-slot name="tbody">
            @foreach ($this->submissions as $submission)
                <tr wire:key='submission-{{ $submission->id }}'
                    class="transition-colors duration-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <span class="text-xs font-mono bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded text-slate-600 dark:text-slate-400">
                            {{ $submission->code }}
                        </span>
                    </td>
                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                        <span class="block text-sm text-gray-800 dark:text-gray-200 font-medium">
                            {{ $submission->service?->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-{{ $submission->status_color }}-100 text-{{ $submission->status_color }}-800 dark:bg-{{ $submission->status_color }}-900/30 dark:text-{{ $submission->status_color }}-500">
                            <span class="h-2 w-2 rounded-full bg-{{ $submission->status_color }}-500"></span>
                            {{ __($submission->status_label) }}
                        </span>
                    </td>
                    <td class="px-3 py-4 text-sm text-gray-500">
                        {{ $submission->created_at->format('d M Y H:i') }}
                    </td>
                    @canany(['submission.update', 'submission.delete'])
                        <td class="size-px whitespace-nowrap">
                            <div class="px-6 py-1.5">
                                <livewire:core.shared-components.item-actions
                                    :editUrl="route('rakaca.landlord.submission.edit', $submission->id)" 
                                    :deleteId="$submission->id"
                                    wire:key="item-actions-{{ $submission->id }}"
                                    :confirmMessage="__('Are you sure you want to delete this submission?')" />
                            </div>
                        </td>
                    @endcanany
                </tr>
            @endforeach
        </x-slot>
    </x-core::table>
</div>
