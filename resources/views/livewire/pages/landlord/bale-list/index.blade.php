<div>
    <x-core::page-header gradient :title="__('Bale List Management')" :subtitle="__('Manage Bale instances and their databases')">
        <x-slot name="action">
            @can('bale-list.create')
                <x-core::button link href="{{ route('rakaca.landlord.bale-list.create') }}" label="{{ __('Add New Bale List') }}">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\BaleList"
        rowView="rakaca::livewire.pages.landlord.bale-list.section.bale-list-row"
        :columns="[
            [
                'key'      => 'name',
                'label'    => __('Instance Name'),
                'sortable' => true,
            ],
            [
                'key'      => 'organization_id',
                'label'    => __('Organization'),
                'sortable' => false,
            ],
            [
                'key'      => 'database_name',
                'label'    => __('Database'),
                'sortable' => false,
            ],
            [
                'key'      => 'is_active',
                'label'    => __('Status'),
                'sortable' => true,
            ],
            [
                'key'      => 'actions',
                'label'    => '',
                'sortable' => false,
            ],
        ]"
        :with="['organization']"
        :searchable="['name', 'slug', 'database_name']"
        sortField="name"
        sortDirection="asc"
        :perPage="20"
    />
</div>

