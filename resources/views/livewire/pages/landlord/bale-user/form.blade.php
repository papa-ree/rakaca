<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Bale User Management'), 'route' => 'rakaca.landlord.bale-user.index'],
    ]" :active="$isEdit ? __('Edit Bale User') : __('Create Bale User')" />

    <x-core::page-header :title="$isEdit ? __('Edit Bale User') : __('Create New Bale User')" :subtitle="$isEdit ? __('Update existing Bale user assignment') : __('Assign a user to a Bale instance')" />

    <div class="max-w-2xl">
        <div class="p-6 bg-white border border-gray-200 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 gap-6">
                    {{-- Bale --}}
                    <div>
                        <x-core::label for="bale_id" :value="__('Bale Instance *')" />
                        <select id="bale_id" wire:model="bale_id" required
                            class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            <option value="">{{ __('Select Bale Instance') }}</option>
                            @foreach ($this->baleLists as $bale)
                                <option value="{{ $bale->id }}">{{ $bale->name }}</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="bale_id" />
                    </div>

                    {{-- User --}}
                    <div>
                        <x-core::label for="user_uuid" :value="__('User *')" />
                        <select id="user_uuid" wire:model="user_uuid" required
                            class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            <option value="">{{ __('Select User') }}</option>
                            @foreach ($this->users as $user)
                                <option value="{{ $user->uuid }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <x-core::input-error for="user_uuid" />
                    </div>

                    {{-- Role --}}
                    <div>
                        <x-core::label for="role" :value="__('Access Role *')" />
                        <select id="role" wire:model="role" required
                            class="block w-full px-4 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="root">Root</option>
                        </select>
                        <x-core::input-error for="role" />
                    </div>

                    {{-- Info note --}}
                    <div
                        class="rounded-lg bg-blue-50 border border-blue-200 px-4 py-3 dark:bg-blue-900/20 dark:border-blue-700">
                        <div class="flex items-start gap-2">
                            <x-lucide-info class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" />
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                {{ __('Only users with an active Bale CMS service can be assigned to a Bale instance.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('rakaca.landlord.bale-user.index') }}" wire:navigate>
                        <x-core::secondary-button type="button">
                            {{ __('Cancel') }}
                        </x-core::secondary-button>
                    </a>
                    <x-core::button type="submit">
                        {{ $isEdit ? __('Update Bale User') : __('Assign User to Bale') }}
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>