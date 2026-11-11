<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-core::stat-card-gradient tone="sky" icon="server" :value="$this->stats['active']"
        :label="__('Layanan Aktif')" :badge="__('Aktif')" :hint="__('Layanan TIK yang Anda miliki')" />

    <x-core::stat-card-gradient tone="amber" icon="clock" :value="$this->stats['pending']"
        :label="__('Pengajuan Menunggu')" :badge="__('Proses')" :hint="__('Menunggu berkas, review, atau proses')" />

    <x-core::stat-card-gradient tone="teal" icon="check-circle-2" :value="$this->stats['done']"
        :label="__('Pengajuan Selesai')" :badge="__('Selesai')" :hint="__('Pengajuan yang sudah tuntas')" />
</div>
