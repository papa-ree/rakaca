<div>
    <x-core::page-header gradient :title="__('Submission Management')" :subtitle="__('Manage service submissions')">
        <x-slot name="action">
            @can('submission.create')
                <x-core::button link href="{{ route('rakaca.landlord.submission.create') }}" label="{{ __('Add New Submission') }}">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\Submission"
        rowView="rakaca::livewire.pages.landlord.submission.section.submission-row"
        :columns="[
            [
                'key'      => 'code',
                'label'    => __('Code'),
                'sortable' => true,
            ],
            [
                'key'      => 'service_id',
                'label'    => __('Service'),
                'sortable' => false,
            ],
            [
                'key'      => 'status',
                'label'    => __('Status'),
                'sortable' => true,
            ],
            [
                'key'      => 'created_at',
                'label'    => __('Date'),
                'sortable' => true,
            ],
            [
                'key'      => 'actions',
                'label'    => '',
                'sortable' => false,
            ],
        ]"
        :with="['service']"
        :searchable="['code', 'status']"
        sortField="created_at"
        sortDirection="desc"
        :perPage="20"
    />
</div>

