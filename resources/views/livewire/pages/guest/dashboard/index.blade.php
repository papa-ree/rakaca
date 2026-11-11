<div class="space-y-4 sm:space-y-5">
    <x-core::page-header gradient :title="__('Halo, ') . (auth()->user()->name ?? __('Guest'))" :subtitle="__('Ajukan layanan TIK dan pantau status pengajuan Anda di satu tempat.')">
        <x-slot name="action">
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                <x-core::button link :href="route('rakaca.guest.submission.create')" :label="__('Ajukan Layanan')" class="w-full justify-center sm:w-auto">
                    <x-slot name="icon"><x-lucide-plus class="size-5" /></x-slot>
                </x-core::button>

                <x-core::button link variant="secondary" :href="route('rakaca.guest.submission.index')" :label="__('Riwayat Pengajuan')" class="w-full justify-center sm:w-auto">
                    <x-slot name="icon"><x-lucide-history class="size-5" /></x-slot>
                </x-core::button>
            </div>
        </x-slot>
    </x-core::page-header>

    <livewire:rakaca.pages.guest.dashboard.section.stats-strip />

    <livewire:rakaca.pages.guest.dashboard.section.submission-status />

    <livewire:rakaca.pages.guest.dashboard.section.service-list />

    <div class="flex flex-col gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700 dark:bg-gray-800">
        <p class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
            <x-lucide-headphones class="size-4 shrink-0 text-gray-400 dark:text-gray-500" />
            {{ __('Butuh bantuan memilih layanan?') }}
        </p>
        <a href="https://wa.me/{{ config('rakaca.support_whatsapp') }}" target="_blank" rel="noopener"
            class="inline-flex items-center gap-1 text-sm font-semibold text-purple-600 hover:underline dark:text-purple-400">
            {{ __('Hubungi support') }}
            <x-lucide-external-link class="size-3.5" />
        </a>
    </div>
</div>
