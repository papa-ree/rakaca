<div id="services" class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
    x-data="{
        q: '',
        names: @js($this->services->map(fn ($service) => mb_strtolower($service->name . ' ' . ($service->description ?? '')))->values()->all()),
        visible(index) {
            const key = this.q.trim().toLowerCase();
            return key === '' || (this.names[index] ?? '').includes(key);
        },
        get hits() {
            const key = this.q.trim().toLowerCase();
            return this.names.filter((name) => name.includes(key)).length;
        }
    }">
    {{-- Card header --}}
    <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                <x-lucide-layers class="size-4" />
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Pilih Layanan') }}</h2>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ __(':count layanan tersedia. Pilih satu untuk mulai mengajukan.', ['count' => $this->services->count()]) }}
                </p>
            </div>
        </div>

        @if($this->services->count() > 3)
            <div class="w-full sm:w-64">
                <x-core::input type="search" x-model="q" :placeholder="__('Cari layanan…')" class="py-2! text-sm!" />
            </div>
        @endif
    </div>

    <div class="p-2 sm:p-3">
        @if($this->services->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <x-lucide-inbox class="size-6" />
                </span>
                <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Belum ada layanan tersedia.') }}</p>
                <p class="mt-1 max-w-sm text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Layanan sedang disiapkan. Hubungi support bila Anda membutuhkan layanan segera.') }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-1 sm:grid-cols-2 sm:gap-x-4">
                @foreach($this->services as $service)
                    @php
                        $palette = \Paparee\Rakaca\Support\ServicePalette::get($service->color);
                        $hasForm = $service->forms->isNotEmpty();
                    @endphp

                    <a @if($hasForm) href="{{ route('rakaca.guest.submission.create', ['service_id' => $service->id]) }}" wire:navigate @endif
                        x-show="visible({{ $loop->index }})"
                        class="group flex items-center gap-3 rounded-lg px-2.5 py-2.5 {{ $hasForm
                            ? 'transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/40'
                            : 'cursor-not-allowed opacity-50' }}">
                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $palette['icon'] }}">
                            <x-dynamic-component :component="'lucide-' . ($service->icon ?? 'layers')" class="size-5 text-white" />
                        </span>

                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-semibold text-gray-900 transition-colors dark:text-white {{ $palette['title'] }}">{{ $service->name }}</span>
                            <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $service->description ?: __('Layanan TIK untuk instansi Anda.') }}
                            </span>
                        </span>

                        @if($hasForm)
                            <span class="hidden shrink-0 text-[11px] font-medium text-gray-400 sm:block dark:text-gray-500">
                                {{ __(':count formulir', ['count' => $service->forms->count()]) }}
                            </span>
                            <x-lucide-chevron-right class="size-4 shrink-0 text-gray-300 transition-transform group-hover:translate-x-0.5 group-hover:text-gray-500 dark:text-gray-600 dark:group-hover:text-gray-300" />
                        @else
                            <span class="shrink-0 text-[11px] font-medium text-gray-400 dark:text-gray-500">{{ __('Belum ada formulir') }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div x-show="hits === 0" x-cloak class="flex flex-col items-center justify-center py-8 text-center">
                <span class="flex size-12 items-center justify-center rounded-full bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <x-lucide-search-x class="size-6" />
                </span>
                <p class="mt-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Layanan tidak ditemukan.') }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Coba kata kunci lain, atau hubungi support.') }}</p>
            </div>
        @endif
    </div>
</div>
