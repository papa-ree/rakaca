<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <x-core::breadcrumb :items="[
        ['label' => __('Submissions'), 'route' => 'rakaca.guest.submission.index'],
    ]" :active="__('New Submission')" />

    {{-- Mobile: stacked, Desktop: 3-col grid with sidebar sticky --}}
    <div class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6" x-data="{
            formId: @entangle('form_id'),
            init() {
                this.$watch('formId', (val) => {
                    $wire.set('form_id', val);
                });
            }
        }">

        {{-- Main Form — full width on mobile, 2/3 on desktop --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md overflow-hidden">
                {{-- Header --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                    <div class="flex items-start gap-3.5">
                        <div class="hidden sm:flex p-3 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg shrink-0">
                            <x-lucide-file-text class="w-6 h-6 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">{{ __('Ajukan Layanan Baru') }}</h2>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ __('Pilih formulir sesuai kebutuhan, lengkapi data, dan unggah berkas pendukung (PDF, opsional).') }}</p>
                        </div>
                    </div>
                </div>

                @include('rakaca::livewire.pages.guest.submission.form', [
                    'cancelRoute' => route('rakaca.guest.submission.index'),
                    'submitLabel' => __('Kirim Pengajuan'),
                ])
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md lg:sticky lg:top-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md">
                            <x-lucide-info class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Buat Pengajuan') }}</h3>
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-semibold">{{ __('Formulir Layanan TI') }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">{{ __('Cara Pengisian') }}</h4>
                        <ul class="space-y-3">
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                <span class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Pilih formulir sesuai layanan (contoh: Zoom Meeting)') }}</span>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-purple-500 shrink-0"></span>
                                <span class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Isi field wajib — tanda') }} <span class="text-red-500">*</span> {{ __('harus diisi') }}</span>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Unggah PDF bila ada (max 3), atau belakangan via Edit') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 p-3.5 flex items-start gap-2.5">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Data Anda terenkripsi. Status awal') }} <span class="font-semibold text-gray-900 dark:text-white">{{ __('Pending') }}</span> — {{ __('akan tampil di Overview & History.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
