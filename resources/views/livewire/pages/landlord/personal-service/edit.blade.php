<div>
    <x-core::page-header :title="__('Edit Customer Service')" :subtitle="__('Update service assignment')">
        <x-slot name="actions">
            <a href="{{ route('rakaca.landlord.personal-service.index') }}" wire:navigate
                class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                <x-lucide-arrow-left class="w-4 h-4" />
                {{ __('Back to List') }}
            </a>
        </x-slot>
    </x-core::page-header>

    <div class="max-w-2xl mx-auto">
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">

            {{-- Header Banner --}}
            <div
                class="p-6 border-b border-gray-100 dark:border-slate-800 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Edit Customer Service') }}</h3>
                <div class="flex items-center gap-2 mt-2">
                    <div
                        class="w-7 h-7 rounded-full bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <span class="text-xs font-bold text-white">
                            {{ strtoupper(substr($personalService->user?->name ?? '?', 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <span
                            class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $personalService->user?->name ?? __('Unknown User') }}</span>
                        <span
                            class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $personalService->user?->email }}</span>
                    </div>
                </div>
            </div>

            <form wire:submit="save" class="p-8 space-y-6">

                {{-- Service Selection --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('Service') }} <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($this->services as $service)
                                            <label wire:key="svc-{{ $service->id }}"
                                                class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all
                                                        {{ $rakaca_service_id === $service->id
                            ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700'
                            : 'bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-800' }}">
                                                <input type="radio" wire:model="rakaca_service_id" value="{{ $service->id }}"
                                                    class="shrink-0 text-indigo-600 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600">
                                                <div class="flex items-center gap-2">
                                                    <div
                                                        class="shrink-0 w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                                                        @if($service->icon)
                                                            <x-dynamic-component :component="'lucide-' . $service->icon"
                                                                class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                                        @else
                                                            <x-lucide-layers class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $service->name }}</span>
                                                </div>
                                            </label>
                        @endforeach
                    </div>

                    @error('rakaca_service_id')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Active Status --}}
                <div
                    class="flex items-center gap-3 p-4 rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700">
                    <input id="actived" type="checkbox" wire:model="actived"
                        class="shrink-0 w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600">
                    <div>
                        <label for="actived"
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">{{ __('Active Status') }}</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ __('Enable or disable this service for the customer') }}</p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-6 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <a href="{{ route('rakaca.landlord.personal-service.index') }}" wire:navigate>
                        <button type="button"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition-all">
                            {{ __('Cancel') }}
                        </button>
                    </a>
                    <button type="submit"
                        class="px-8 py-2.5 bg-linear-to-r from-indigo-500 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-indigo-500/25 flex items-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>