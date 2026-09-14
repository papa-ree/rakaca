<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
    <x-core::breadcrumb :items="[
        ['label' => __('Submissions'), 'route' => 'rakaca.guest.submission.index'],
    ]" :active="__('Edit Submission')" />

    <div class="mt-4 sm:mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Main Form — full width mobile, 2/3 desktop --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-900 rounded-xl sm:rounded-2xl border border-gray-200 dark:border-slate-800 shadow-sm sm:shadow-xl overflow-hidden">
                {{-- Header — compact mobile --}}
                <div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 bg-gradient-to-r from-indigo-50/80 to-purple-50/80 dark:from-indigo-950/20 dark:to-purple-950/20 border-b border-gray-100 dark:border-slate-800">
                    <div class="flex items-start gap-3">
                        <div class="hidden sm:flex p-2.5 bg-indigo-600 rounded-xl shrink-0">
                            <x-lucide-file-text class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white leading-tight">{{ $submission->form?->name ?? __('Edit Pengajuan') }}</h2>
                            <p class="mt-1 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-mono text-xs bg-white dark:bg-slate-800 px-2 py-0.5 rounded border">{{ $submission->code }}</span>
                                <span class="mx-1">·</span> {{ $submission->created_at->diffForHumans() }}
                                <span class="ml-2 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $submission->statusColor }}-50 text-{{ $submission->statusColor }}-700 ring-1 ring-{{ $submission->statusColor }}-600/20">{{ __($submission->statusLabel) }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <form wire:submit="save" class="p-4 sm:p-6 lg:p-8 space-y-5 sm:space-y-6">
                    {{-- Dynamic Fields --}}
                    @if ($submission->form && $submission->form->meta && isset($submission->form->meta['fields']))
                        <div class="space-y-4 sm:space-y-5">
                            @foreach ($submission->form->meta['fields'] as $field)
                                <div class="space-y-1.5">
                                    <x-core::label :for="'items_' . $field['key']" :value="__($field['label'])" class="text-sm" />

                                    @if ($field['type'] === 'string')
                                        <x-core::input :id="'items_' . $field['key']" type="text" class="block w-full mt-1 rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" :required="$field['required'] ?? false" />

                                    @elseif ($field['type'] === 'textarea')
                                        <x-core::textarea :id="'items_' . $field['key']" class="block w-full mt-1 rounded-xl" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" rows="4" />

                                    @elseif ($field['type'] === 'number')
                                        <x-core::input :id="'items_' . $field['key']" type="number" inputmode="numeric" class="block w-full mt-1 rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" :required="$field['required'] ?? false" />

                                    @elseif ($field['type'] === 'email')
                                        <x-core::input :id="'items_' . $field['key']" type="email" inputmode="email" class="block w-full mt-1 rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :placeholder="__($field['placeholder'] ?? '')" :required="$field['required'] ?? false" />

                                    @elseif ($field['type'] === 'date')
                                        <x-core::input :id="'items_' . $field['key']" type="date" class="block w-full mt-1 rounded-xl py-3" wire:model="items.{{ $field['key'] }}" :required="$field['required'] ?? false" />

                                    @elseif ($field['type'] === 'select')
                                        <select :id="'items_' . $field['key']" wire:model="items.{{ $field['key'] }}" class="block w-full mt-1 rounded-xl border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white bg-white text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 py-3 px-4 text-sm sm:text-base">
                                            <option value="">-- {{ __('Pilih') }} --</option>
                                            @if(!empty($field['options']))
                                                @foreach($field['options'] as $option)
                                                    <option value="{{ $option }}">{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    @elseif ($field['type'] === 'checkbox')
                                        <label class="flex items-center gap-3 py-2.5 px-1 cursor-pointer min-h-[44px]">
                                            <input type="checkbox" :id="'items_' . $field['key']" wire:model="items.{{ $field['key'] }}" class="h-5 w-5 rounded border-gray-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-900" />
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($field['placeholder'] ?? $field['label']) }}</span>
                                        </label>

                                    @elseif ($field['type'] === 'file')
                                        <input type="file" :id="'items_' . $field['key']" wire:model="items.{{ $field['key'] }}" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="block w-full text-sm file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/30 dark:file:text-indigo-300 hover:file:bg-indigo-100 file:cursor-pointer cursor-pointer py-1" />
                                        <p class="mt-1 text-xs text-gray-500">PDF/JPG/PNG/DOC, max 10MB</p>
                                    @endif

                                    @error('items.' . $field['key'])
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        @if(in_array($submission->status, ['pending', 'rejected']))
                            <div class="mt-6 p-3 sm:p-4 bg-amber-50/60 dark:bg-amber-900/10 rounded-xl border border-amber-200 dark:border-amber-800/30">
                                <div class="flex items-start gap-2.5 mb-3">
                                    <div class="p-1.5 bg-amber-100 dark:bg-amber-900/30 rounded-lg shrink-0 mt-0.5">
                                        <x-lucide-upload class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h5 class="text-sm font-bold text-gray-900 dark:text-white leading-tight">Upload Berkas Pendukung</h5>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">PDF, maksimal 3 file, 5 MB per file, opsional — dapat diisi belakangan</p>
                                    </div>
                                </div>
                                @if($submission && $submission->uploads && $submission->uploads->count() > 0)
                                    <div class="mb-3 space-y-1.5">
                                        <p class="text-xs font-semibold text-gray-600 dark:text-gray-400">Berkas terunggah ({{ $submission->uploads->count() }}/3):</p>
                                        @foreach($submission->uploads as $up)
                                            <div class="flex items-center justify-between gap-2 p-2.5 bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700">
                                                <a href="{{ Storage::url($up->file_path) }}" target="_blank" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline truncate min-w-0 flex-1">{{ $up->original_name }} <span class="text-xs text-gray-500">({{ number_format($up->size/1024, 1) }} KB)</span></a>
                                                <button type="button" wire:click="deleteExistingUpload('{{ $up->id }}')" wire:confirm="Hapus berkas ini?" class="shrink-0 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg min-h-[36px] min-w-[36px] flex items-center justify-center">
                                                    <x-lucide-trash-2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                @if(($submission->uploads->count() ?? 0) < 3)
                                    <x-core::upload-zone wire:model.live="uploads" accept="application/pdf,.pdf" :maxSize="5120" multiple label="Tap untuk pilih PDF atau drag & drop" hint="PDF, maksimal 3 file, 5 MB per file, opsional — dapat diisi belakangan" />
                                    @error('uploads') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    @error('uploads.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                @else
                                    <p class="text-xs text-amber-600 dark:text-amber-400">Maksimal 3 file tercapai.</p>
                                @endif
                            </div>
                        @else
                            <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                                <p class="text-sm text-slate-600 dark:text-slate-400 flex items-center gap-2"><x-lucide-lock class="w-4 h-4" /> {{ __('Upload terkunci pada status') }} <span class="font-semibold">{{ $submission->statusLabel }}</span></p>
                                @if($submission->uploads->count() > 0)
                                    <div class="mt-3 space-y-1.5">
                                        @foreach($submission->uploads as $up)
                                            <div class="flex items-center justify-between p-2 bg-white dark:bg-slate-800 rounded-lg border">
                                                <a href="{{ Storage::url($up->file_path) }}" target="_blank" class="text-sm text-indigo-600 hover:underline truncate">{{ $up->original_name }}</a>
                                                <span class="text-xs text-gray-500">{{ number_format($up->size/1024, 1) }} KB</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endif

                    {{-- Actions — mobile stacked, desktop row --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 pt-6 border-t border-gray-100 dark:border-slate-800">
                        <x-core::secondary-button link href="{{ route('rakaca.guest.submission.index') }}" label="{{ __('Batal') }}" class="w-full sm:w-auto justify-center py-3" />
                        <x-core::button type="submit" spinner="save" label="{{ __('Perbarui Pengajuan') }}" class="w-full sm:w-auto justify-center py-3 text-base">
                            <x-slot name="icon"><x-lucide-check class="w-4 h-4" /></x-slot>
                        </x-core::button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar — below on mobile, sticky on desktop --}}
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-b from-indigo-50/90 to-purple-50/90 dark:from-indigo-950/20 dark:to-purple-950/20 rounded-xl sm:rounded-2xl border border-indigo-100 dark:border-indigo-800/30 shadow-sm lg:sticky lg:top-6 overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-indigo-100/60 dark:border-indigo-800/20">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow">
                            <x-lucide-pencil class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white truncate">{{ __('Edit Pengajuan') }}</h3>
                            <p class="text-xs font-mono text-indigo-600 dark:text-indigo-400 truncate">{{ $submission->code }}</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 sm:p-6 space-y-4">
                    <div>
                        <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-2">{{ __('Status') }}</h4>
                        <span class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-{{ $submission->statusColor }}-50 text-{{ $submission->statusColor }}-700 ring-1 ring-inset ring-{{ $submission->statusColor }}-600/20 dark:bg-{{ $submission->statusColor }}-900/30 dark:text-{{ $submission->statusColor }}-400">
                            {{ __($submission->statusLabel) }}
                        </span>
                        @if($submission->status === 'ditutup')
                            <p class="mt-2 text-xs text-slate-500 leading-relaxed">Pengajuan ditutup — tidak dapat diedit atau upload.</p>
                        @elseif($submission->status === 'review')
                            <p class="mt-2 text-xs text-blue-600 dark:text-blue-400 leading-relaxed">Sedang direview — upload terkunci.</p>
                        @elseif($submission->status === 'approved')
                            <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 leading-relaxed">Sudah disetujui — tidak dapat diedit.</p>
                        @elseif($submission->status === 'rejected')
                            <p class="mt-2 text-xs text-red-600 dark:text-red-400 leading-relaxed">Ditolak — Anda masih bisa perbaiki dan kirim ulang (akan kembali menunggu).</p>
                        @endif
                    </div>
                    <div class="rounded-xl bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 p-3 flex items-start gap-2.5">
                        <x-lucide-shield-check class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <p class="text-xs leading-relaxed text-gray-600 dark:text-gray-400">Perubahan akan tersimpan. Jika sebelumnya <span class="font-semibold">Ditolak</span>, status akan kembali <span class="font-semibold">Menunggu</span>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
