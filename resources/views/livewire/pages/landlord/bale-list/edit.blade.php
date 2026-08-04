<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Bale List Management'), 'route' => 'rakaca.landlord.bale-list.index'],
    ]" :active="__('Edit Bale List')" />

    <x-core::page-header :title="__('Edit Bale List')" :subtitle="__('Update Bale instance and database configuration')" />

    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 border-l-4 border-l-amber-500 rounded-r-xl dark:bg-amber-950/20 dark:border-amber-800/40 dark:border-l-amber-600 max-w-4xl">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <x-lucide-alert-triangle class="h-5 w-5 text-amber-500 dark:text-amber-400" />
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-amber-900 dark:text-amber-200">
                        {{ __('Warning Decryption') }}
                    </h4>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                        {{ session('warning') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-4xl">
        <div class="p-6 bg-white border border-gray-200 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Organization --}}
                    <div class="col-span-2">
                        <x-core::label for="organization_id" :value="__('Organization *')" />
                        <select id="organization_id" wire:model="organization_id" required
                            class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            <option value="">{{ __('Select Organization') }}</option>
                            @foreach ($this->organizations as $org)
                                <option value="{{ $org->id }}">{{ $org->name }}</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="organization_id" />
                    </div>

                    {{-- Name --}}
                    <div>
                        <x-core::label for="name" :value="__('Instance Name *')" />
                        <x-core::input id="name" type="text" wire:model.live="name" required />
                        <x-core::input-error for="name" />
                    </div>

                    {{-- Slug --}}
                    <div>
                        <x-core::label for="slug" :value="__('Slug *')" />
                        <x-core::input id="slug" type="text" wire:model="slug" required />
                        <x-core::input-error for="slug" />
                    </div>

                    <div class="col-span-2 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ __('Database Configuration') }}
                        </h3>
                    </div>

                    {{-- DB Host --}}
                    <div>
                        <x-core::label for="database_host" :value="__('Database Host *')" />
                        <x-core::input id="database_host" type="text" wire:model="database_host" required />
                        <x-core::input-error for="database_host" />
                    </div>

                    {{-- DB Name --}}
                    <div>
                        <x-core::label for="database_name" :value="__('Database Name *')" />
                        <x-core::input id="database_name" type="text" wire:model="database_name" required />
                        <x-core::input-error for="database_name" />
                    </div>

                    {{-- DB Username --}}
                    <div>
                        <x-core::label for="database_username" :value="__('Database Username *')" />
                        <x-core::input id="database_username" type="text" wire:model="database_username" required />
                        <x-core::input-error for="database_username" />
                    </div>

                    {{-- DB Password --}}
                    <div>
                        <x-core::label for="database_password" :value="__('Database Password')" />
                        <x-core::input id="database_password" type="password" wire:model="database_password" />
                        <x-core::input-error for="database_password" />
                    </div>

                    <div class="col-span-2 border-t border-gray-100 dark:border-gray-700 pt-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('Others') }}</h3>
                    </div>

                    {{-- Storage Prefix --}}
                    <div>
                        <x-core::label for="storage_prefix" :value="__('Storage Prefix')" />
                        <x-core::input id="storage_prefix" type="text" wire:model="storage_prefix" />
                        <x-core::input-error for="storage_prefix" />
                    </div>

                    {{-- Is Active --}}
                    <div class="flex items-center">
                        <div class="flex items-center h-5">
                            <input id="is_active" type="checkbox" wire:model="is_active"
                                class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active"
                                class="font-medium text-gray-700 dark:text-gray-300">{{ __('Status Active') }}</label>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('rakaca.landlord.bale-list.index') }}" wire:navigate>
                        <x-core::secondary-button type="button">
                            {{ __('Cancel') }}
                        </x-core::secondary-button>
                    </a>
                    <x-core::button type="submit">
                        {{ __('Update Bale List') }}
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>