<div>
    <x-core::page-header :title="__('Edit Analytic')" :subtitle="__('Update analytic integration details')">
        <x-slot name="action">
            <a href="{{ route('rakaca.landlord.analytic.index') }}" wire:navigate
                class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                <x-lucide-arrow-left class="w-4 h-4" />
                {{ __('Back to List') }}
            </a>
        </x-slot>
    </x-core::page-header>

    <div class="mt-8 max-w-3xl">
        <form wire:submit="save" class="space-y-6">
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('Analytic Details') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Update the analytic integration information below') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    {{-- Bale Instance --}}
                    <div>
                        <x-core::label for="bale_id" :value="__('Bale Instance *')" />
                        <select id="bale_id" wire:model="bale_id"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">{{ __('Select Bale Instance') }}</option>
                            @foreach ($this->baleInstances as $bale)
                                <option value="{{ $bale->id }}">{{ $bale->name }} ({{ $bale->slug }})</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="bale_id" class="mt-2" />
                    </div>

                    {{-- Provider --}}
                    <div>
                        <x-core::label for="provider" :value="__('Provider *')" />
                        <select id="provider" wire:model="provider"
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="umami">Umami</option>
                        </select>
                        <x-core::input-error for="provider" class="mt-2" />
                    </div>

                    {{-- Website ID --}}
                    <div>
                        <x-core::label for="website_id" :value="__('Website ID (UUID) *')" />
                        <x-core::input id="website_id" type="text" class="mt-1 block w-full" wire:model="website_id"
                            placeholder="xxxx-xxxx-xxxx-xxxx" />
                        <x-core::input-error for="website_id" class="mt-2" />
                    </div>

                    {{-- Domain --}}
                    <div>
                        <x-core::label for="domain" :value="__('Domain')" />
                        <x-core::input id="domain" type="text" class="mt-1 block w-full" wire:model="domain"
                            placeholder="example.com" />
                        <x-core::input-error for="domain" class="mt-2" />
                    </div>

                    {{-- Enabled --}}
                    <div class="flex items-center gap-x-3">
                        {{-- <x-core::checkbox id="enabled" wire:model="enabled" /> --}}
                        <x-core::label for="enabled" :value="__('Enabled')" />
                        <x-core::input-error for="enabled" class="mt-2" />
                    </div>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-gray-50 dark:bg-slate-800/50 border-t border-gray-100 dark:border-slate-700 flex justify-end">
                <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ __('Update Analytic') }}
                </button>
            </div>
        </form>
    </div>
</div>