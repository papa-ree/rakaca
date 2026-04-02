<div>
    <x-core::page-header gradient :title="__('Bale User Management')" :subtitle="__('Manage user assignments to Bale instances')">
        <x-slot name="action">
            @can('bale-user.create')
                <x-core::button link href="{{ route('rakaca.landlord.bale-user.create') }}" label="{{ __('Add New Bale User') }}">
                    <x-slot name="icon">
                        <x-lucide-plus class="w-5 h-5" />
                    </x-slot>
                </x-core::button>
            @endcan
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\BaleUser"
        rowView="rakaca::livewire.pages.landlord.bale-user.section.bale-user-row"
        :columns="[
            [
                'key'      => 'user_id',
                'label'    => __('User'),
                'sortable' => false,
            ],
            [
                'key'      => 'bale_id',
                'label'    => __('Bale Instance'),
                'sortable' => false,
            ],
            [
                'key'      => 'role',
                'label'    => __('Role'),
                'sortable' => true,
            ],
            [
                'key'      => 'created_at',
                'label'    => __('Assigned At'),
                'sortable' => true,
            ],
            [
                'key'      => 'actions',
                'label'    => '',
                'sortable' => false,
            ],
        ]"
        :with="['user', 'bale']"
        :searchable="['role']"
        sortField="created_at"
        sortDirection="desc"
        :perPage="20"
    />
</div>

