<div>
    <x-core::page-header gradient :title="__('Submission Management')" :subtitle="__('Manage service submissions')">
        <x-slot name="action">
            <div class="flex flex-wrap items-center gap-2">
                @can('submission.export')
                    <x-core::secondary-button link-reload href="{{ route('rakaca.landlord.submission.export', 'csv') }}"
                        label="{{ __('CSV') }}" />

                    <x-core::secondary-button link-reload href="{{ route('rakaca.landlord.submission.export', 'xlsx') }}"
                        label="{{ __('XLSX') }}" />
                @endcan

                @can('submission.create')
                    <x-core::button link href="{{ route('rakaca.landlord.submission.create') }}" label="{{ __('Tambah Pengajuan') }}">
                        <x-slot name="icon">
                            <x-lucide-plus class="w-5 h-5" />
                        </x-slot>
                    </x-core::button>
                @endcan
            </div>
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\RakacaSubmission"
        rowView="rakaca::livewire.pages.landlord.submission.section.submission-row"
        :columns="[
            [
                'key'      => 'code',
                'label'    => __('Pengajuan'),
                'sortable' => true,
            ],
            [
                'key'      => 'nip',
                'label'    => __('NIP'),
                'sortable' => false,
            ],
            [
                'key'      => 'form.service.name',
                'label'    => __('Layanan'),
                'sortable' => false,
            ],
            [
                'key'      => 'status',
                'label'    => __('Status'),
                'sortable' => true,
            ],
            [
                'key'      => 'sla_due_at',
                'label'    => __('SLA'),
                'sortable' => true,
            ],
            [
                'key'      => 'created_at',
                'label'    => __('Tanggal'),
                'sortable' => true,
            ],
            [
                'key'      => 'actions',
                'label'    => '',
                'sortable' => false,
            ],
        ]"
        :with="['form.service', 'user', 'uploads', 'response']"
        :searchable="['code', 'status']"
        sortField="created_at"
        sortDirection="asc"
        :perPage="20"
    />
</div>

