<div>
    <x-core::page-header gradient :title="__('Analytic Management')" :subtitle="__('Manage analytic integration for tenant instances')">
        <x-slot name="action">
            @can('analytic.create')
                <x-core::button link href="{{ route('rakaca.landlord.analytic.create') }}" label="{{ __('Add New Analytic') }}">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\TenantAnalytics"
        rowView="rakaca::livewire.pages.landlord.analytic.section.analytic-row"
        :columns="[
            [
                'key'      => 'bale_id',
                'label'    => __('Bale Instance'),
                'sortable' => true,
            ],
            [
                'key'      => 'provider',
                'label'    => __('Provider'),
                'sortable' => true,
            ],
            [
                'key'      => 'website_id',
                'label'    => __('Website ID'),
                'sortable' => true,
            ],
            [
                'key'      => 'domain',
                'label'    => __('Domain'),
                'sortable' => true,
            ],
            [
                'key'      => 'enabled',
                'label'    => __('Status'),
                'sortable' => true,
            ],
            [
                'key'      => 'created_at',
                'label'    => __('Created At'),
                'sortable' => true,
            ],
            [
                'key'      => 'actions',
                'label'    => '',
                'sortable' => false,
            ],
        ]"
        :searchable="['domain', 'provider', 'website_id']"
        sortField="created_at"
        sortDirection="desc"
        :perPage="10"
    />
</div>

