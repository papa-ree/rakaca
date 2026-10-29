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
                        <x-core::secondary-button link href="{{ $cancelRoute }}" label="{{ __('Batal') }}" class="w-full sm:w-auto justify-center" />
                        <x-core::button type="submit" spinner="save" label="{{ $submitLabel }}" class="w-full sm:w-auto justify-center">
                            <x-slot name="icon"><x-lucide-send class="w-4 h-4" /></x-slot>
                        </x-core::button>
                    </div>
                </form>
