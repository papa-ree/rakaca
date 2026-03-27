<div>
    <x-core::breadcrumb :items="[['label' => __('Services'), 'route' => 'rakaca.landlord.service.index']]"
        :active="__('Create Service')" />

    <div class="max-w-4xl mx-auto mt-6" x-data="{ 
            serviceName: $wire.entangle('name'), 
            serviceSlug: $wire.entangle('slug'), 
            serviceIcon: $wire.entangle('icon'), 
            actived: $wire.entangle('actived').live 
        }">
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">
            {{-- Header Banner --}}
            <div
                class="p-6 border-b border-gray-100 dark:border-slate-800 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Service Details') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ __('Define the technical details and visual identifier for the new Rakaca service') }}
                </p>
            </div>

            <form
                @submit.prevent="$wire.call('save', { ...Object.fromEntries(new FormData($event.target)), actived: actived })"
                class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Name --}}
                    <div>
                        <x-core::label for="name" :value="__('Service Name')" />
                        <x-core::input id="name" name="name" type="text" class="block w-full mt-1" x-model="serviceName"
                            required placeholder="e.g. Internet Desa" />
                        <x-core::input-error for="name" class="mt-2" />
                    </div>

                    {{-- Slug --}}
                    <div>
                        <x-core::label for="slug" :value="__('Slug')" />
                        <x-core::input id="slug" name="slug" type="text" class="block w-full mt-1" x-model="serviceSlug"
                            x-slug="serviceName" required placeholder="internet-desa" />
                        <p class="mt-1 text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                            {{ __('Auto-generated from service name') }}
                        </p>
                        <x-core::input-error for="slug" class="mt-2" />
                    </div>
                </div>

                {{-- Icon --}}
                <div>
                    <x-core::label for="icon" :value="__('Icon (Lucide Name)')" />
                    <div class="flex gap-x-4 items-center mt-1">
                        <div class="relative grow">
                            <x-core::input id="icon" name="icon" type="text" class="block w-full" x-model="serviceIcon"
                                placeholder="e.g. globe, activity, users" />
                        </div>
                        <div
                            class="shrink-0 w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800/50 flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-sm">
                            <span class="text-indigo-600 dark:text-indigo-400">
                                <template x-if="serviceIcon">
                                    <x-dynamic-component component="lucide-help-circle"
                                        x-bind:is="'x-lucide-' + serviceIcon.replace('lucide-', '')" class="w-6 h-6" />
                                </template>
                                <template x-if="!serviceIcon">
                                    <x-lucide-help-circle class="w-6 h-6 text-slate-400" />
                                </template>
                            </span>
                        </div>
                    </div>
                    <x-core::input-error for="icon" class="mt-2" />
                </div>

                {{-- Actived Toggle --}}
                <div
                    class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                            <x-lucide-toggle-right class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <label for="service-active-toggle"
                                class="text-sm font-bold text-gray-900 dark:text-white cursor-pointer">
                                {{ __('Enable Service') }}
                            </label>
                            <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                {{ __('Show this service in the customer portal') }}
                            </p>
                        </div>
                    </div>
                    <label for="service-active-toggle" class="relative inline-block w-12 h-6 cursor-pointer">
                        <input type="checkbox" id="service-active-toggle" x-model="actived" class="peer sr-only">
                        <span
                            class="absolute inset-0 bg-gray-300 dark:bg-slate-700 rounded-full transition-colors duration-300 peer-checked:bg-emerald-500"></span>
                        <span
                            class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-300 peer-checked:translate-x-6"></span>
                    </label>
                </div>
                <x-core::input-error for="actived" class="mt-2" />

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
                    <x-core::secondary-button link href="{{ route('rakaca.landlord.service.index') }}" label="Cancel" />

                    <x-core::button type="submit" label="Create Service" spinner="save">
                        <x-slot name="icon"><x-lucide-plus class="w-4 h-4" /></x-slot>
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>