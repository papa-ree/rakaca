<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Submissions'), 'route' => 'rakaca.guest.submission.index'],
    ]"    :active="__('New Submission')" />

    <div class="mt-6" x-data="{
            formId: @entangle('form_id'),
            items: $wire.entangle('items').live,
            selectedForm: null,
            fields: [],
            init() {
                this.$watch('formId', (val) => {
                    $wire.set('form_id', val);
                });
            }
        }">
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Form Main --}}
            <div class="flex-1 min-w-0 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">
                <form wire:submit="save" class="p-8 space-y-6">

                    {{-- Section: Select Form --}}
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                <x-lucide-file-text class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ __('Pilih Formulir') }}
                                </h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                    {{ __('Pilih layanan TI yang ingin diajukan') }}
                                </p>
                            </div>
                        </div>

                        <select wire:model.live="form_id" id="form_id"
                            class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">-- {{ __('Pilih Formulir') }} --</option>
                            @foreach ($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->name }} ({{ $form->service?->name ?? '-' }})</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="form_id" class="mt-2" />
                    </div>

                    {{-- Dynamic Fields --}}
                    @if ($selectedForm && $selectedForm->meta && isset($selectedForm->meta['fields']))
                        <div class="border-t border-gray-100 dark:border-slate-800 pt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <x-lucide-list class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ __('Isi Formulir') }}
                                    </h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                        {{ __('Lengkapi data berikut sesuai kebutuhan') }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-5">
                                @foreach ($selectedForm->meta['fields'] as $field)
                                    <div>
                                        <x-core::label :for="'items_' . $field['key']" :value="__($field['label'])" />

                                        @if ($field['type'] === 'string')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="text"
                                                class="block w-full mt-1"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'textarea')
                                            <x-core::textarea
                                                :id="'items_' . $field['key']"
                                                class="block w-full mt-1"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                rows="3"
                                            />

                                        @elseif ($field['type'] === 'number')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="number"
                                                class="block w-full mt-1"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'email')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="email"
                                                class="block w-full mt-1"
                                                wire:model="items.{{ $field['key'] }}"
                                                :placeholder="__($field['placeholder'] ?? '')"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'date')
                                            <x-core::input
                                                :id="'items_' . $field['key']"
                                                type="date"
                                                class="block w-full mt-1"
                                                wire:model="items.{{ $field['key'] }}"
                                                :required="$field['required'] ?? false"
                                            />

                                        @elseif ($field['type'] === 'select')
                                            <select
                                                :id="'items_' . $field['key']"
                                                wire:model="items.{{ $field['key'] }}"
                                                class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                            >
                                                <option value="">-- {{ __('Pilih') }} --</option>
                                            </select>

                                        @elseif ($field['type'] === 'checkbox')
                                            <div class="flex items-center gap-2 mt-1">
                                                <input
                                                    type="checkbox"
                                                    :id="'items_' . $field['key']"
                                                    wire:model="items.{{ $field['key'] }}"
                                                    class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900"
                                                />
                                                <label :for="'items_' . $field['key']" class="text-sm text-gray-700 dark:text-gray-300">
                                                    {{ __($field['placeholder'] ?? $field['label']) }}
                                                </label>
                                            </div>

                                        @elseif ($field['type'] === 'file')
                                            <div class="mt-1">
                                                <input
                                                    type="file"
                                                    :id="'items_' . $field['key']"
                                                    wire:model="items.{{ $field['key'] }}"
                                                    class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/20 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/30"
                                                />
                                            </div>
                                        @endif

                                        @error('items.' . $field['key'])
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
                        <x-core::secondary-button link href="{{ route('rakaca.guest.submission.index') }}"
                            label="{{ __('Cancel') }}" />

                        <x-core::button type="submit" spinner="save"
                            label="{{ __('Submit') }}">
                            <x-slot name="icon">
                                <x-lucide-send class="w-4 h-4" />
                            </x-slot>
                        </x-core::button>
                    </div>
                </form>
            </div>

            {{-- Sidebar --}}
            <div class="w-full lg:w-80 shrink-0">
                <div
                    class="bg-linear-to-b from-indigo-50/80 to-purple-50/80 dark:from-indigo-900/15 dark:to-purple-900/15 rounded-2xl border border-indigo-100 dark:border-indigo-800/30 shadow-xl overflow-hidden lg:sticky lg:top-6">

                    <div class="p-6 border-b border-indigo-100/60 dark:border-indigo-800/20">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg shadow-indigo-500/25">
                                <x-lucide-info class="w-5 h-5 text-white" />
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ __('Buat Pengajuan') }}
                                </h3>
                                <p class="text-[10px] text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-medium">
                                    {{ __('Formulir Layanan TI') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-1">
                                {{ __('Cara Pengisian') }}
                            </h4>
                            <ul class="space-y-2.5">
                                <li class="flex items-start gap-2.5">
                                    <div class="shrink-0 mt-0.5 w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Pilih formulir layanan yang sesuai') }}
                                    </span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="shrink-0 mt-0.5 w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Isi semua field yang diperlukan') }}
                                    </span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="shrink-0 mt-0.5 w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Kirim dan tunggu persetujuan admin') }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
