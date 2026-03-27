<div>
    <x-core::breadcrumb :items="[['label' => __('Analytics'), 'route' => 'rakaca.landlord.analytic.index']]" :active="__('Create Analytic')" />

    <div class="max-w-4xl mx-auto mt-6">
        {{-- Setup Guide --}}
        <div class="mb-6 bg-linear-to-br from-indigo-50 to-purple-50 dark:from-slate-800/50 dark:to-slate-800/30 border border-indigo-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 p-2 bg-white dark:bg-slate-900 rounded-xl shadow-xs">
                    <x-lucide-lightbulb class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">{{ __('Quick Setup Guide') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Select Bale Instance') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-bold">2</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Enter Umami Website ID') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-pink-600 text-white flex items-center justify-center text-[10px] font-bold">3</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Save & Start Tracking') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">
            {{-- Header Banner --}}
            <div class="p-6 border-b border-gray-100 dark:border-slate-800 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Analytic Integration') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ __('Configure external analytics tracking for your platform') }}
                </p>
            </div>

            <form wire:submit="save" class="p-8 space-y-8">
                {{-- Section 1: Core Configuration --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 bg-indigo-500 rounded-full"></div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest">{{ __('Instance & Provider') }}</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Bale Instance --}}
                        <div>
                            <x-core::label for="bale_id" :value="__('Bale Instance')" />
                            <select id="bale_id" wire:model="bale_id" class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-slate-700 dark:bg-slate-800 dark:text-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all sm:text-sm font-medium">
                                <option value="">{{ __('Select Instance') }}</option>
                                @foreach ($this->baleInstances as $bale)
                                    <option value="{{ $bale->id }}">{{ $bale->name }} ({{ $bale->slug }})</option>
                                @endforeach
                            </select>
                            <x-core::input-error for="bale_id" class="mt-2" />
                        </div>

                        {{-- Provider --}}
                        <div>
                            <x-core::label for="provider" :value="__('Analytics Provider')" />
                            <select id="provider" wire:model="provider" class="mt-1 block w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-slate-700 dark:bg-slate-800 dark:text-gray-300 bg-white text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all sm:text-sm font-medium">
                                <option value="umami">Umami Analytics</option>
                            </select>
                            <x-core::input-error for="provider" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Section 2: Technical Credentials --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-6 bg-purple-500 rounded-full"></div>
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-widest">{{ __('Technical Details') }}</h4>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Website ID --}}
                        <div class="md:col-span-2">
                            <x-core::label for="website_id" :value="__('Website ID (UUID)')" />
                            <x-core::input id="website_id" type="text" class="block w-full mt-1" wire:model="website_id" required placeholder="e.g. 550e8400-e29b-41d4-a716-446655440000" />
                            <p class="mt-1.5 text-[10px] text-gray-500 uppercase tracking-wider">{{ __('Find this ID in your Umami dashboard settings') }}</p>
                            <x-core::input-error for="website_id" class="mt-2" />
                        </div>

                        {{-- Domain --}}
                        <div class="md:col-span-2">
                            <x-core::label for="domain" :value="__('Custom Domain (Optional)')" />
                            <x-core::input id="domain" type="text" class="block w-full mt-1" wire:model="domain" placeholder="e.g. stats.yourdomain.com" />
                            <x-core::input-error for="domain" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- Actived Toggle --}}
                <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                            <x-lucide-activity class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <label for="analytic-enabled-toggle" class="text-sm font-bold text-gray-900 dark:text-white cursor-pointer">
                                {{ __('Tracking Status') }}
                            </label>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                {{ __('Immediately enable data collection') }}
                            </p>
                        </div>
                    </div>
                    <label for="analytic-enabled-toggle" class="relative inline-block w-12 h-6 cursor-pointer">
                        <input type="checkbox" id="analytic-enabled-toggle" wire:model="enabled" class="peer sr-only">
                        <span class="absolute inset-0 bg-gray-300 dark:bg-slate-700 rounded-full transition-colors duration-300 peer-checked:bg-emerald-500"></span>
                        <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-300 peer-checked:translate-x-6"></span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
                    <x-core::secondary-button link href="{{ route('rakaca.landlord.analytic.index') }}" label="Cancel" />
                    
                    <x-core::button type="submit" label="Create Analytic" spinner="save">
                        <x-slot name="icon"><x-lucide-check class="w-4 h-4" /></x-slot>
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>