<div>
    <x-core::breadcrumb :items="[['label' => __('Personal Services'), 'route' => 'rakaca.landlord.personal-service.index']]" :active="__('Assign Customer Service')" />

    <div class="max-w-4xl mx-auto">
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">

            {{-- Header Banner --}}
            <div
                class="p-6 border-b border-gray-100 dark:border-slate-800 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Customer Service Details') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ __('Select a user and assign one or more services') }}
                </p>
            </div>

            <form wire:submit="save" class="p-8 space-y-6">

                {{-- User Selection --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('Select User') }} <span class="text-red-500">*</span>
                    </label>

                    {{-- Search input for users --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-lucide-search class="w-4 h-4 text-slate-400" />
                        </div>
                        <x-core::input type="text" wire:model.live.debounce.300ms="userSearch" autocomplete="off"
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                            placeholder="{{ __('Search user by name or email...') }}" />
                    </div>

                    {{-- User list --}}
                    <div
                        class="max-h-48 overflow-y-auto rounded-xl border border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-800/50 divide-y divide-gray-100 dark:divide-slate-700">
                        {{-- Loading Skeleton --}}
                        <div wire:loading wire:target="userSearch" class="contents">
                            @for ($i = 0; $i < 3; $i++)
                                <div class="flex items-center gap-3 px-4 py-2.5 animate-pulse">
                                    <div class="shrink-0 size-4 rounded-full bg-slate-200 dark:bg-slate-700"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded w-1/3"></div>
                                        <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded w-1/2"></div>
                                        <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded w-1/4"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        {{-- User List items --}}
                        <div wire:loading.remove wire:target="userSearch" class="contents">
                            @forelse($this->users as $user)
                                <label wire:key="user-{{ $user->uuid ?? $user->id }}"
                                    class="flex items-center gap-3 px-4 py-2.5 cursor-pointer hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors
                                                                                            {{ $user_uuid === ($user->uuid ?? (string) $user->id) ? 'bg-indigo-50 dark:bg-indigo-900/20' : '' }}">
                                    <input type="radio" wire:model="user_uuid" value="{{ $user->uuid ?? $user->id }}"
                                        class="shrink-0 size-4 bg-transparent border-line-3 rounded-full shadow-2xs text-primary focus:ring-0 focus:ring-offset-0 checked:bg-primary-checked checked:border-primary-checked disabled:opacity-50 disabled:pointer-events-none">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->username }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </label>
                            @empty
                                <p class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500 text-center">
                                    {{ __('No users found') }}
                                </p>
                            @endforelse
                        </div>
                    </div>

                    @error('user_uuid')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Service Selection --}}
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ __('Select Services') }} <span class="text-red-500">*</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @forelse($this->services as $service)
                                            <label wire:key="svc-{{ $service->id }}"
                                                class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all
                                                                                                                                                                                                                                                                                                        {{ in_array($service->id, $rakaca_service_ids)
                            ? 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-300 dark:border-indigo-700'
                            : 'bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 hover:border-indigo-200 dark:hover:border-indigo-800' }}">
                                                <input type="checkbox" wire:model="rakaca_service_ids" value="{{ $service->id }}"
                                                    class="shrink-0 rounded text-indigo-600 focus:ring-indigo-500 dark:bg-slate-700 dark:border-slate-600">
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
                        @empty
                            <p class="col-span-2 px-4 py-3 text-sm text-gray-400 text-center">
                                {{ __('No active services available') }}
                            </p>
                        @endforelse
                    </div>

                    @error('rakaca_service_ids')
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
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300 cursor-pointer">{{ __('Service Status') }}</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ __('Mark the assigned service(s) as active') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="pt-6 border-t border-gray-100 dark:border-slate-800 flex items-center justify-between">
                    <x-core::secondary-button type="button" link
                        href="{{ route('rakaca.landlord.personal-service.index') }}" label="Cancel" />

                    <x-core::button type="submit" label="Assign Service" spinner="save">
                        <x-slot name="icon"><x-lucide-user-check class="w-4 h-4" /></x-slot>
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>