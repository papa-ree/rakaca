<div>
    <x-core::page-header gradient title="Service Management" subtitle="Kelola layanan Rakaca">
        <x-slot name="action">
            @can('service.create')
                <x-core::button link href="{{ route('rakaca.landlord.service.create') }}" label="Add New Service">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\Service"
        rowView="rakaca::livewire.pages.landlord.service.section.service-row"
        :columns="[
            [
                'key'      => 'name',
                'label'    => __('Service Name'),
                'sortable' => true,
            ],
            [
                'key'      => 'slug',
                'label'    => __('Slug'),
                'sortable' => true,
            ],
            [
                'key'      => 'actived',
                'label'    => __('Status'),
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

