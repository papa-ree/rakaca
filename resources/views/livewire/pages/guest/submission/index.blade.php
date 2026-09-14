<div>
    <x-core::page-header gradient title="Pengajuan Saya" subtitle="Daftar pengajuan layanan TI yang telah dibuat">
        <x-slot name="action">
            <x-core::button link href="{{ route('rakaca.guest.submission.create') }}" label="Buat Pengajuan Baru">
                <x-slot name="icon">
                    <x-lucide-plus class="w-5 h-5" />
                </x-slot>
            </x-core::button>
        </x-slot>
    </x-core::page-header>

    <livewire:core-shared-components::data-table
        model="Paparee\Rakaca\Models\RakacaSubmission"
        rowView="rakaca::livewire.pages.guest.submission.section.guest-submission-row"
        :columns="[
            [
                'key'      => 'code',
                'label'    => __('Kode'),
                'sortable' => true,
            ],
            [
                'key'      => 'status',
                'label'    => __('Status'),
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
        :with="['form.service', 'uploads']"
        :constraints="['user_uuid' => auth()->user()->uuid]"
        :searchable="['code', 'status']"
        sortField="created_at"
        sortDirection="desc"
        :perPage="20"
    />
</div>
