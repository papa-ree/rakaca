                <form wire:submit="save" class="p-6 sm:p-8 space-y-6">
                    {{-- Select Form --}}
                    <div class="space-y-1.5">
                        <x-core::label>
                            {{ __('Pilih Formulir') }} <span class="text-red-500">*</span>
                        </x-core::label>
                        <x-core::select-dropdown
                            model="form_id"
                            :items="$forms->map(fn ($form) => ['id' => $form->id, 'label' => $form->name . ' — ' . ($form->service?->name ?? '-')])->values()->all()"
                            labelField="label"
                            valueField="id"
                            :placeholder="__('Pilih Formulir')" />
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
                                            <x-core::date-picker
                                                :model="'items.' . $field['key']"
                                                :name="'items_' . $field['key']"
                                                mode="date"
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
                                            @continue($source === 'srikandi')
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

                            {{-- Sumber Berkas: pilih salah satu. Alpine memberi respons instan, server tetap sumber kebenaran. --}}
                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700 space-y-4" x-data="{ mode: '{{ $source }}' }">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Sumber Berkas') }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pilih salah satu: unggah berkas sendiri atau gunakan surat dari Srikandi.') }}</p>
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <button type="button" x-on:click="mode = 'upload'; $wire.pilihUpload()"
                                        class="flex items-start gap-3 rounded-xl border p-4 text-left transition-all duration-200 cursor-pointer"
                                        x-bind:class="mode === 'upload' ? 'border-purple-500 bg-purple-50/60 ring-2 ring-purple-500/20 dark:bg-purple-900/10' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700'">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-300">
                                            <x-lucide-upload class="w-5 h-5" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('Unggah Berkas Sendiri') }}</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('PDF, maksimal 3 file, 5 MB per file.') }}</span>
                                        </span>
                                    </button>

                                    <button type="button" x-on:click="mode = 'srikandi'; $wire.pilihSrikandi()"
                                        class="flex items-start gap-3 rounded-xl border p-4 text-left transition-all duration-200 cursor-pointer"
                                        x-bind:class="mode === 'srikandi' ? 'border-purple-500 bg-purple-50/60 ring-2 ring-purple-500/20 dark:bg-purple-900/10' : 'border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700'">
                                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            <x-lucide-search class="w-5 h-5" />
                                        </span>
                                        <span class="min-w-0">
                                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">{{ __('Cari Surat Srikandi') }}</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Gunakan nomor naskah surat masuk dari Srikandi.') }}</span>
                                        </span>
                                    </button>
                                </div>

                                <x-core::input-error for="source" class="mt-1" />

                                <div x-show="mode === 'srikandi'" class="space-y-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                                        <x-core::label for="nomor_naskah" class="text-sm font-medium">
                                            {{ __('Nomor Naskah') }} <span class="text-red-500">*</span>
                                        </x-core::label>
                                        <div class="flex flex-col sm:flex-row gap-2">
                                            <x-core::input id="nomor_naskah" type="text" class="block w-full"
                                                wire:model="nomor_naskah" wire:keydown.enter.prevent="cariSurat"
                                                placeholder="000.8.3.4/KH/271/405.03.2/2026" />
                                            <x-core::button type="button" wire:click="cariSurat" spinner="cariSurat"
                                                label="{{ __('Cari') }}" class="w-full sm:w-auto justify-center shrink-0">
                                                <x-slot name="icon"><x-lucide-search class="w-4 h-4" /></x-slot>
                                            </x-core::button>
                                        </div>
                                        <x-core::input-error for="nomor_naskah" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Tulis nomor naskah lengkap termasuk tahun. Pencarian harus sama persis.') }}</p>

                                        @if ($suratError)
                                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800 dark:border-amber-800/40 dark:bg-amber-900/20 dark:text-amber-300">
                                                {{ $suratError }}
                                            </div>
                                        @endif

                                        @foreach ($suratResults as $hasil)
                                            <div class="space-y-1 rounded-xl border border-gray-200 dark:border-gray-700 p-3">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <p class="font-mono text-sm font-semibold text-gray-900 dark:text-white break-all">{{ $hasil['nomor_naskah'] }}</p>
                                                    <x-core::button type="button" variant="secondary" wire:click="gunakanSurat('{{ $hasil['id'] }}')"
                                                        label="{{ __('Gunakan surat ini') }}" class="justify-center">
                                                        <x-slot name="icon"><x-lucide-check class="w-4 h-4" /></x-slot>
                                                    </x-core::button>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $hasil['tanggal_naskah'] ? \Illuminate\Support\Carbon::parse($hasil['tanggal_naskah'])->translatedFormat('d M Y') : '-' }}
                                                </p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $hasil['hal'] ?? '-' }}</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $hasil['ringkasan'] ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengirim') }}: {{ $hasil['pengirim'] ?? '-' }}</p>
                                            </div>
                                        @endforeach

                                        @if ($surat)
                                            <div class="rounded-xl border border-teal-200 bg-teal-50/60 p-3 dark:border-teal-800/40 dark:bg-teal-900/10">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <p class="text-xs font-semibold uppercase tracking-wider text-teal-700 dark:text-teal-300">{{ __('Surat terpilih') }}</p>
                                                    <button type="button" wire:click="gantiSurat" class="text-xs font-semibold text-purple-600 hover:underline dark:text-purple-400 cursor-pointer">{{ __('Ganti') }}</button>
                                                </div>
                                                <p class="mt-1 font-mono text-sm text-gray-900 dark:text-white break-all">{{ $surat['nomor_naskah'] }}</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $surat['hal'] ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Pengirim') }}: {{ $surat['pengirim'] ?? '-' }}</p>
                                            </div>
                                        @endif
                                    </div>

                                {{-- Zona upload selalu ada di DOM agar scope Alpine tidak rusak saat ganti mode. --}}
                                <div x-show="mode !== 'srikandi'">
                                    <x-core::upload-zone
                                        wire:model.live="uploads"
                                        accept="application/pdf,.pdf"
                                        :maxSize="5120"
                                        :maxFiles="3"
                                        remove-method="removeUpload"
                                        multiple
                                        label="Tap untuk pilih PDF atau drag & drop"
                                        hint="PDF, maksimal 3 file, 5 MB per file"
                                    />
                                    <x-core::input-error for="uploads" class="mt-2" />
                                    <x-core::input-error for="uploads.*" class="mt-1" />
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <x-core::secondary-button link href="{{ $cancelRoute }}" label="{{ __('Batal') }}" class="w-full sm:w-auto justify-center" />
                        <x-core::button type="submit" spinner="save" label="{{ $submitLabel }}" class="w-full sm:w-auto justify-center">
                            <x-slot name="icon"><x-lucide-send class="w-4 h-4" /></x-slot>
                        </x-core::button>
                    </div>
                </form>
