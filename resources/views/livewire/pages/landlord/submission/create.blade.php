<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <x-core::breadcrumb :items="[
        ['label' => __('Submissions'), 'route' => 'rakaca.landlord.submission.index'],
    ]" :active="__('Tambah Pengajuan')" />

    <div class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

        {{-- Main --}}
        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md overflow-hidden">
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-teal-50/60 to-emerald-50/60 dark:from-teal-900/10 dark:to-emerald-900/10">
                    <div class="flex items-start gap-3.5">
                        <div
                            class="hidden sm:flex p-3 bg-linear-to-br from-teal-500 to-emerald-600 rounded-xl shadow-lg shrink-0">
                            <x-lucide-file-plus-2 class="w-6 h-6 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">
                                {{ __('Tambah Pengajuan Manual') }}
                            </h2>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                                {{ __('Buat pengajuan atas nama user terdaftar. Status awal Menunggu Berkas.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Pemohon --}}
                <div class="p-6 sm:p-8 pb-0 space-y-1.5">
                    <x-core::label for="userSearch" class="text-sm font-medium">
                        {{ __('Pemohon') }} <span class="text-red-500">*</span>
                    </x-core::label>

                    <x-core::input id="userSearch" type="search" wire:model.live.debounce.300ms="userSearch"
                        placeholder="{{ __('Cari nama atau email...') }}" class="block w-full" />

                    <x-core::select-dropdown model="user_uuid" :items="$userOptions" valueField="uuid"
                        labelField="name" placeholder="{{ __('Pilih user') }}" />

                    <x-core::input-error for="user_uuid" />
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Daftar dibatasi 50 hasil; gunakan pencarian untuk user lain.') }}
                    </p>
                </div>

                @include('rakaca::livewire.pages.guest.submission.form', [
                    'cancelRoute' => route('rakaca.landlord.submission.index'),
                    'submitLabel' => __('Simpan Pengajuan'),
                ])
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md lg:sticky lg:top-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-linear-to-br from-teal-500 to-emerald-600 rounded-xl shadow-md">
                            <x-lucide-info class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Catatan') }}</h3>
                            <p class="text-xs text-teal-600 dark:text-teal-400 uppercase tracking-wider font-semibold">
                                {{ __('Pengajuan Manual') }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <ul class="space-y-3">
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-teal-500 shrink-0"></span>
                            <span
                                class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Pilih pemohon lalu formulir layanan yang sesuai.') }}</span>
                        </li>
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span
                                class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Pengajuan tercatat sebagai milik pemohon, bukan akun admin.') }}</span>
                        </li>
                        <li class="flex gap-2.5">
                            <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span
                                class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Lanjutkan proses lewat halaman detail pengajuan.') }}</span>
                        </li>
                    </ul>

                    <div
                        class="rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 p-3.5 flex items-start gap-2.5">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                            {{ __('Membutuhkan permission :permission.', ['permission' => 'submission.create']) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
