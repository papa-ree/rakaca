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
            <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm sm:shadow-xl overflow-hidden">
                {{-- Header — compact on mobile --}}
                <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 bg-gradient-to-r from-indigo-50/80 to-purple-50/80 dark:from-indigo-950/20 dark:to-purple-950/20 border-b border-gray-100 dark:border-slate-800">
                    <div class="flex items-start gap-3">
                        <div class="hidden sm:flex p-2.5 bg-indigo-600 rounded-xl shrink-0">
                            <x-lucide-file-text class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-tight">Ajukan Layanan Baru</h2>
                            <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Pilih formulir sesuai kebutuhan, lengkapi data, dan unggah berkas pendukung (PDF, opsional).</p>
                        </div>
                    </div>
                </div>

                <form wire:submit="save" class="p-4 sm:p-6 lg:p-8 space-y-5 sm:space-y-6">
                    {{-- Select Form — full width, large tap target on mobile --}}
                    <div>
                        <label for="form_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('Pilih Formulir') }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="form_id" id="form_id"
                            class="block w-full rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white bg-white text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-sm sm:text-base py-3 px-4 shadow-sm">
                            <option value="">-- {{ __('Pilih Formulir') }} --</option>
                            @foreach ($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }} — {{ $form->service?->name ?? '-' }}</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="form_id" class="mt-2" />
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Form akan menyesuaikan layanan (misal Zoom Meeting).</p>
                    </div>

                    {{-- Dynamic Fields — mobile: single col, desktop: 2-col where appropriate --}}
                    @if ($selectedForm && $selectedForm->meta && isset($selectedForm->meta['fields']))
                        <div class="pt-5 sm:pt-6 border-t border-gray-100 dark:border-slate-800">
                            <div class="flex items-center gap-2.5 mb-4">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <x-lucide-list class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Isi Formulir') }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Lengkapi data berikut') }}</p>
                                </div>
                            </div>

                            <div class="space-y-4 sm:space-y-5">
                                @foreach ($selectedForm->meta['fields'] as $field)
                                    <div class="space-y-1.5">
                                        <x-core::label :for="'items_' . $field['key']" :value="__($field['label'])" class="text-sm" />

                                        @if ($field['type'] === 'string')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="text"
                                                class="block w-full mt-1 rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'textarea')
                                            <x-core::textarea
                                                :id="'items_' . $field['key']"
                                                class="block w-full mt-1 rounded-xl"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                rows="4"
                                            />

                                        @elseif ($field['type'] === 'number')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="number"
                                                inputmode="numeric"
                                                class="block w-full mt-1 rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'email')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="email"
                                                inputmode="email"
                                                class="block w-full mt-1 rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'date')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="date"
                                                class="block w-full mt-1 rounded-xl py-3"
                                                wire:model="items.{{ $field['key'] }}"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'select')
                                            <select
                                                :id="'items_' . $field['key']"
                                                wire:model="items.{{ $field['key'] }}"
                                                class="block w-full mt-1 rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white bg-white text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 py-3 px-4 text-sm sm:text-base"
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
                                                <input
                                                    type="checkbox"
                                                    :id="'items_' . $field['key']"
                                                    wire:model="items.{{ $field['key'] }}"
                                                    class="h-5 w-5 rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-900"
                                                />
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($field['placeholder'] ?? $field['label']) }}</span>
                                            </label>

                                        @elseif ($field['type'] === 'file')
                                            <input
                                                type="file"
                                                :id="'items_' . $field['key']"
                                                wire:model="items.{{ $field['key'] }}"
                                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                                class="block w-full text-sm file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/40 file:cursor-pointer cursor-pointer py-1"
                                            />
                                            <p class="mt-1 text-xs text-gray-500">{{ __('PDF/JPG/PNG/DOC, max 10MB') }}</p>
                                        @endif

                                        @error('items.' . $field['key'])
                                            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <x-core::upload-zone
                                wire:model.live="uploads"
                                accept="application/pdf,.pdf"
                                :maxSize="5120"
                                multiple
                                label="Tap untuk pilih PDF atau drag &amp; drop"
                                hint="PDF, maksimal 3 file, 5 MB per file, opsional — dapat diisi belakangan"
                                class="mt-6"
                            />
                            @error('uploads') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                            @error('uploads.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Actions — mobile: stacked full-width, desktop: row --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-6 border-t border-gray-100 dark:border-slate-800">
                        <x-core::secondary-button link href="{{ route('rakaca.guest.submission.index') }}" label="{{ __('Batal') }}" class="w-full sm:w-auto justify-center py-3" />
                        <x-core::button type="submit" spinner="save" label="{{ __('Kirim Pengajuan') }}" class="w-full sm:w-auto justify-center py-3 text-base">
                            <x-slot name="icon"><x-lucide-send class="w-4 h-4" /></x-slot>
                        </x-core::button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar — on mobile below form, on desktop sticky right --}}
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-b from-indigo-50/90 to-purple-50/90 dark:from-indigo-950/20 dark:to-purple-950/20 rounded-xl sm:rounded-2xl border border-indigo-100 dark:border-indigo-800/30 shadow-sm lg:sticky lg:top-6 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-indigo-100/60 dark:border-indigo-800/20">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow">
                            <x-lucide-info class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">Buat Pengajuan</h3>
                            <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-medium">Formulir Layanan TI</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">Cara Pengisian</h4>
                        <ul class="space-y-2.5">
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-indigo-500 shrink-0"></span>
                                <span class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">Pilih formulir sesuai layanan (contoh: Zoom Meeting)</span>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-purple-500 shrink-0"></span>
                                <span class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">Isi field wajib — tanda <span class="text-red-500">*</span> harus diisi</span>
                            </li>
                            <li class="flex gap-2.5">
                                <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">Unggah PDF bila ada (max 3), atau belakangan via Edit</span>
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 p-3 flex items-start gap-2.5">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">Data Anda terenkripsi. Status awal <span class="font-semibold">Pending</span> — akan tampil di Overview & History.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
