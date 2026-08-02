<div>
    <x-core::page-header gradient title="Form Management" subtitle="Kelola formulir dinamis untuk layanan TI">
        <x-slot name="action">
            @can('form.create')
                <x-core::button link href="{{ route('rakaca.landlord.form.create') }}" label="Add New Form">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\Form"
        rowView="rakaca::livewire.pages.landlord.form.section.form-row"
        :columns="[
            [
                'key'      => 'name',
                'label'    => __('Form Name'),
                'sortable' => true,
            ],
            [
                'key'      => 'slug',
                'label'    => __('Slug'),
                'sortable' => true,
            ],
            [
                'key'      => 'service',
                'label'    => __('Service'),
                'sortable' => false,
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
