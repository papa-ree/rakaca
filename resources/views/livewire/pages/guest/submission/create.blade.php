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

                <form wire:submit="save" class="p-6 sm:p-8 space-y-6">
                    {{-- Select Form --}}
                    <div class="space-y-1.5">
                        <x-core::label for="form_id" class="text-sm font-medium">
                            {{ __('Pilih Formulir') }} <span class="text-red-500">*</span>
                        </x-core::label>
                        <select wire:model.live="form_id" id="form_id"
                            class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm sm:text-base py-3 px-4 transition-all duration-200 shadow-xs">
                            <option value="">-- {{ __('Pilih Formulir') }} --</option>
                            @foreach ($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }} — {{ $form->service?->name ?? '-' }}</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="form_id" />
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Form akan menyesuaikan layanan (misal Zoom Meeting).') }}</p>
                    </div>

                    {{-- Dynamic Fields --}}
                    @if ($selectedForm && $selectedForm->meta && isset($selectedForm->meta['fields']))
                        <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="p-2.5 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                                    <x-lucide-list class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ __('Isi Formulir') }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Lengkapi data berikut') }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 sm:space-y-5">
                                @foreach ($selectedForm->meta['fields'] as $field)
                                    <div class="space-y-1.5">
                                        <x-core::label :for="'items_' . $field['key']" class="text-sm font-medium">
                                            {{ __($field['label']) }}
                                            @if(!empty($field['required']))<span class="text-red-500 ml-0.5">*</span>@endif
                                        </x-core::label>

                                        @if ($field['type'] === 'string')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="text"
                                                class="block w-full rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                            />

                                        @elseif ($field['type'] === 'textarea')
                                            <x-core::textarea
                                                :id="'items_' . $field['key']"
                                                class="block w-full rounded-xl"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                rows="4"
                                            />

                                        @elseif ($field['type'] === 'number')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="number"
                                                inputmode="numeric"
                                                class="block w-full rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                            />

                                        @elseif ($field['type'] === 'email')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="email"
                                                inputmode="email"
                                                class="block w-full rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                            />

                                        @elseif ($field['type'] === 'date')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="date"
                                                class="block w-full rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                            />

                                        @elseif ($field['type'] === 'select')
                                            <select
                                                id="items_{{ $field['key'] }}"
                                                wire:model="items.{{ $field['key'] }}"
                                                class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent py-3 px-4 text-sm sm:text-base transition-all duration-200"
                                            >
                                                <option value="">-- {{ __('Pilih') }} --</option>
                                                @if(!empty($field['options']))
                                                    @foreach($field['options'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                @endif
                                            </select>

                                        @elseif ($field['type'] === 'checkbox')
                                            <label class="flex items-center gap-3 py-2.5 px-1 cursor-pointer min-h-[44px]">
                                                <x-core::checkbox
                                                    :id="'items_' . $field['key']"
                                                    wire:model="items.{{ $field['key'] }}"
                                                />
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($field['placeholder'] ?? $field['label']) }}</span>
                                            </label>

                                        @elseif ($field['type'] === 'file')
                                            <input
                                                type="file"
                                                id="items_{{ $field['key'] }}"
                                                wire:model="items.{{ $field['key'] }}"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                class="block w-full text-sm file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 dark:file:bg-purple-900/30 dark:file:text-purple-300 hover:file:bg-purple-100 dark:hover:file:bg-purple-900/40 file:cursor-pointer cursor-pointer py-1"
                                            />
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('PDF/JPG/PNG/DOC, max 10MB') }}</p>
                                        @endif

                                        <x-core::input-error :for="'items.' . $field['key']" />
                                    </div>
                                @endforeach
                            </div>

                            <x-core::upload-zone
                                wire:model.live="uploads"
                                accept="application/pdf,.pdf"
                                :maxSize="5120"
                                multiple
                                label="Tap untuk pilih PDF atau drag & drop"
                                hint="PDF, maksimal 3 file, 5 MB per file, opsional — dapat diisi belakangan"
                                class="mt-6"
                            />
                            <x-core::input-error for="uploads" class="mt-2" />
                            <x-core::input-error for="uploads.*" class="mt-1" />
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <x-core::secondary-button link href="{{ route('rakaca.guest.submission.index') }}" label="{{ __('Batal') }}" class="w-full sm:w-auto justify-center" />
                        <x-core::button type="submit" spinner="save" label="{{ __('Kirim Pengajuan') }}" class="w-full sm:w-auto justify-center">
                            <x-slot name="icon"><x-lucide-send class="w-4 h-4" /></x-slot>
                        </x-core::button>
                    </div>
                </form>
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
