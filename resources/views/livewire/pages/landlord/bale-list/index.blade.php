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

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 border-l-4 border-l-rose-500 rounded-r-xl dark:bg-rose-950/20 dark:border-rose-800/40 dark:border-l-rose-600">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 animate-pulse">
                    <x-lucide-x-circle class="h-5 w-5 text-rose-500 dark:text-rose-400" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-rose-900 dark:text-rose-200">
                        {{ __('Connection Error') }}
                    </h4>
                    <p class="text-xs text-rose-700 dark:text-rose-300 mt-0.5">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

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

