<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    @php
        $status = $submission->status instanceof \Paparee\Rakaca\Enums\SubmissionStatus
            ? $submission->status
            : \Paparee\Rakaca\Enums\SubmissionStatus::fromLegacy($submission->status);
    @endphp

    <x-core::breadcrumb :items="[
        ['label' => __('Pengajuan'), 'route' => 'rakaca.guest.submission.index'],
    ]" :active="__('Upload Berkas')" />

    <div class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Main Form — full width mobile, 2/3 desktop --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md overflow-hidden">
                {{-- Header --}}
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-amber-50/50 to-indigo-50/50 dark:from-amber-900/10 dark:to-indigo-900/10">
                    <div class="flex items-start gap-3.5">
                        <div class="hidden sm:flex p-3 bg-linear-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg shrink-0">
                            <x-lucide-upload class="w-6 h-6 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">{{ $submission->form?->name ?? __('Lengkapi Berkas') }}</h2>
                            <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-mono text-xs bg-white dark:bg-gray-700 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white">#{{ $submission->code }}</span>
                                <span class="mx-1">·</span> dibuat {{ $submission->created_at->diffForHumans() }}
                                <span class="ml-2 px-3 py-1 text-xs font-semibold text-{{ $status->color() }}-700 bg-{{ $status->color() }}-100 rounded-full dark:bg-{{ $status->color() }}-900/50 dark:text-{{ $status->color() }}-300 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-{{ $status->color() }}-500 animate-pulse"></span>
                                    {{ $status->label() }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Notifikasi auto-cancel --}}
                <div class="mx-6 mt-6 p-4 bg-linear-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-2xl border border-amber-200 dark:border-amber-800">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 bg-amber-600 rounded-xl shadow-md shrink-0">
                            <x-lucide-alert-triangle class="w-5 h-5 text-white" />
                        </div>
                        <p class="text-xs sm:text-sm text-amber-900 dark:text-amber-200 leading-relaxed">
                            Tiket akan <span class="font-semibold">dibatalkan otomatis</span> jika berkas tidak dikirim dalam
                            <span class="font-semibold">{{ config('rakaca.ticket.auto_cancel_hours', 72) }} jam</span> sejak dibuat.
                        </p>
                    </div>
                </div>

                <form wire:submit="submitFiles" class="p-6 sm:p-8 space-y-6">
                    {{-- Dynamic Fields --}}
                    @if ($submission->form && $submission->form->meta && isset($submission->form->meta['fields']))
                        <div class="space-y-4 sm:space-y-5">
                            @foreach ($submission->form->meta['fields'] as $field)
                                <div class="space-y-1.5">
                                    <x-core::label :for="'items_' . $field['key']" class="text-sm font-medium">
                                        {{ __($field['label']) }}
                                        @if(!empty($field['required']))<span class="text-red-500 ml-0.5">*</span>@endif
                                    </x-core::label>

                                    @if ($field['type'] === 'string')
                                        <x-core::input :id="'items_' . $field['key']" type="text" class="block w-full rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" />

                                    @elseif ($field['type'] === 'textarea')
                                        <x-core::textarea :id="'items_' . $field['key']" class="block w-full rounded-xl" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" rows="4" />

                                    @elseif ($field['type'] === 'number')
                                        <x-core::input :id="'items_' . $field['key']" type="number" inputmode="numeric" class="block w-full rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" />

                                    @elseif ($field['type'] === 'email')
                                        <x-core::input :id="'items_' . $field['key']" type="email" inputmode="email" class="block w-full rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" />

                                    @elseif ($field['type'] === 'date')
                                        <x-core::input :id="'items_' . $field['key']" type="date" class="block w-full rounded-xl py-3" wire:model="items.{{ $field['key'] }}" />

                                    @elseif ($field['type'] === 'select')
                                        <select id="items_{{ $field['key'] }}" wire:model="items.{{ $field['key'] }}" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent py-3 px-4 text-sm sm:text-base transition-all duration-200">
                                            <option value="">-- {{ __('Pilih') }} --</option>
                                            @if(!empty($field['options']))
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    @elseif ($field['type'] === 'checkbox')
                                        <label class="flex items-center gap-3 py-2.5 px-1 cursor-pointer min-h-[44px]">
                                            <x-core::checkbox :id="'items_' . $field['key']" wire:model="items.{{ $field['key'] }}" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($field['placeholder'] ?? $field['label']) }}</span>
                                        </label>

                                    @elseif ($field['type'] === 'file')
                                        <input type="file" id="items_{{ $field['key'] }}" wire:model="items.{{ $field['key'] }}" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="block w-full text-sm file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 dark:file:bg-amber-900/30 dark:file:text-amber-300 hover:file:bg-amber-100 file:cursor-pointer cursor-pointer py-1" />
                                        <p class="text-xs text-gray-500 dark:text-gray-400">PDF/JPG/PNG/DOC, max 10MB</p>
                                    @endif

                                    <x-core::input-error :for="'items.' . $field['key']" />
                                </div>
                            @endforeach
                        </div>

                        {{-- Upload Berkas Pendukung --}}
                        <div class="mt-6 p-5 bg-linear-to-r from-amber-50/70 to-orange-50/70 dark:from-amber-900/10 dark:to-orange-900/10 rounded-2xl border border-amber-200 dark:border-amber-800">
                            <div class="flex items-start gap-3 mb-4">
                                <div class="p-2.5 bg-amber-600 rounded-xl shadow-md shrink-0">
                                    <x-lucide-paperclip class="w-5 h-5 text-white" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h5 class="text-base font-bold text-gray-900 dark:text-white leading-tight">{{ __('Upload Berkas Pendukung') }}</h5>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('PDF, maksimal 3 file, 5 MB per file, opsional') }}</p>
                                </div>
                            </div>
                            @if($submission && $submission->uploads && $submission->uploads->count() > 0)
                                <div class="mb-4 space-y-2">
                                    <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">{{ __('Berkas terunggah') }} ({{ $submission->uploads->count() }}/3):</p>
                                    @foreach($submission->uploads as $up)
                                        <div class="flex items-center justify-between gap-2 p-3 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                                            <a href="{{ Storage::url($up->file_path) }}" target="_blank" class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline truncate min-w-0 flex-1">{{ $up->original_name }} <span class="text-xs text-gray-500 dark:text-gray-400">({{ number_format($up->size/1024, 1) }} KB)</span></a>
                                            <button type="button" wire:click="deleteExistingUpload('{{ $up->id }}')" wire:confirm="{{ __('Hapus berkas ini?') }}" class="shrink-0 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors cursor-pointer">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(($submission->uploads->count() ?? 0) < 3)
                                <x-core::upload-zone wire:model.live="uploads" accept="application/pdf,.pdf" :maxSize="5120" multiple label="Tap untuk pilih PDF atau drag & drop" hint="PDF, maksimal 3 file, 5 MB per file" />
                                <x-core::input-error for="uploads" class="mt-2" />
                                <x-core::input-error for="uploads.*" class="mt-1" />
                            @else
                                <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">{{ __('Maksimal 3 file tercapai.') }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <x-core::secondary-button type="button" wire:click="saveItems" spinner="saveItems" label="{{ __('Simpan Draf') }}" class="w-full sm:w-auto justify-center">
                            <x-slot name="icon"><x-lucide-save class="w-4 h-4" /></x-slot>
                        </x-core::secondary-button>
                        <div class="flex flex-col-reverse sm:flex-row items-center gap-3 w-full sm:w-auto">
                            <x-core::secondary-button link href="{{ route('rakaca.guest.submission.index') }}" label="{{ __('Batal') }}" class="w-full sm:w-auto justify-center" />
                            <x-core::button type="submit" spinner="submitFiles" label="{{ __('Kirim Berkas untuk Review') }}" class="w-full sm:w-auto justify-center">
                                <x-slot name="icon"><x-lucide-send class="w-4 h-4" /></x-slot>
                            </x-core::button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-md lg:sticky lg:top-6 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-linear-to-r from-amber-50/50 to-indigo-50/50 dark:from-amber-900/10 dark:to-indigo-900/10">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-linear-to-br from-amber-500 to-indigo-600 rounded-xl shadow-md">
                            <x-lucide-upload class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white truncate">{{ __('Upload Berkas') }}</h3>
                            <p class="text-xs font-mono text-purple-600 dark:text-purple-400 font-medium truncate">#{{ $submission->code }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">{{ __('Status') }}</h4>
                        <span class="px-3 py-1 text-xs font-semibold text-{{ $status->color() }}-700 bg-{{ $status->color() }}-100 rounded-full dark:bg-{{ $status->color() }}-900/50 dark:text-{{ $status->color() }}-300 inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $status->color() }}-500 animate-pulse"></span>
                            {{ $status->label() }}
                        </span>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">{{ $status->description() }}</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 p-3.5 flex items-start gap-2.5">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">{{ __('Simpan draf untuk kembali lagi; kirim untuk menyerahkan berkas ke petugas.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>