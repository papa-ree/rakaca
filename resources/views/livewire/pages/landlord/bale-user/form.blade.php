<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Bale User Management'), 'route' => 'rakaca.landlord.bale-user.index'],
    ]" :active="$isEdit ? __('Edit Bale User') : __('Create Bale User')" />

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">
            <form wire:submit="save">
                {{-- Bale Instance --}}
                <div class="p-6 border-b border-gray-100 dark:border-slate-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                            <x-lucide-building-2 class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Bale Instance') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Select the target Bale instance') }}</p>
                        </div>
                    </div>
                    <x-core::select-dropdown
                        model="bale_id"
                        :items="$this->baleLists"
                        placeholder="{{ __('Select Bale Instance') }}"
                    />
                    <x-core::input-error for="bale_id" />
                </div>

                {{-- User --}}
                <div class="p-6 border-b border-gray-100 dark:border-slate-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <x-lucide-user class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('User') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Choose the user to assign') }}</p>
                        </div>
                    </div>
                    <x-core::select-dropdown
                        model="user_uuid"
                        :items="$this->users"
                        placeholder="{{ __('Select User') }}"
                    />
                    <x-core::input-error for="user_uuid" />
                </div>

                {{-- Role --}}
                <div class="p-6 border-b border-gray-100 dark:border-slate-800">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                            <x-lucide-shield class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Access Role') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Define the permission level') }}</p>
                        </div>
                    </div>
                    <x-core::select-dropdown
                        model="role"
                        :items="[
                            ['itemTitle' => __('User'), 'itemSlug' => 'user'],
                            ['itemTitle' => __('Admin'), 'itemSlug' => 'admin'],
                            ['itemTitle' => __('Root'), 'itemSlug' => 'root'],
                        ]"
                        placeholder="{{ __('Select Role') }}"
                    />
                    <x-core::input-error for="role" />
                </div>

                {{-- Info Note --}}
                <div class="mx-6 mb-6">
                    <div class="flex items-start gap-3 p-4 bg-linear-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                        <div class="p-2 bg-amber-600 rounded-lg shadow-lg shrink-0">
                            <x-lucide-info class="w-4 h-4 text-white" />
                        </div>
                        <p class="text-sm text-amber-700 dark:text-amber-300">
                            {{ __('Only users with an active Bale CMS service can be assigned to a Bale instance.') }}
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between px-6 py-4 bg-gray-50/50 dark:bg-slate-800/30 border-t border-gray-100 dark:border-slate-800">
                    <x-core::secondary-button link href="{{ route('rakaca.landlord.bale-user.index') }}"
                        label="{{ __('Cancel') }}" />
                    <x-core::button type="submit" label="{{ $isEdit ? __('Update Bale User') : __('Assign User to Bale') }}"
                        spinner="save">
                        <x-slot name="icon">
                            <x-lucide-check class="w-4 h-4" />
                        </x-slot>
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>
