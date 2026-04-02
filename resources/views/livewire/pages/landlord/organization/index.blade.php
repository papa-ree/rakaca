<div>
    <x-core::page-header gradient :title="__('Organization Management')" :subtitle="__('Manage Bale organizations')">
        <x-slot name="action">
            @can('organization.create')
                <x-core::button link href="{{ route('rakaca.landlord.organization.create') }}" label="{{ __('Add New Organization') }}">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\Organization"
        rowView="rakaca::livewire.pages.landlord.organization.section.organization-row"
        :columns="[
            [
                'key'      => 'name',
                'label'    => __('Organization Name'),
                'sortable' => true,
            ],
            [
                'key'      => 'slug',
                'label'    => __('Slug'),
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
        :searchable="['name', 'slug']"
        sortField="name"
        sortDirection="asc"
        :perPage="20"
    />
</div>