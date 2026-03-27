<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Organization Management'), 'route' => 'rakaca.landlord.organization.index'],
    ]" :active="__('Edit Organization')" />

    <x-core::page-header :title="__('Edit Organization')" :subtitle="__('Update organization information')" />

    <div class="max-w-3xl">
        <div class="p-6 bg-white border border-gray-200 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
            <form wire:submit="save" class="space-y-6">
                {{-- Name --}}
                <div>
                    <x-core::label for="name" :value="__('Organization Name *')" />
                    <x-core::input id="name" type="text" wire:model.live="name" required
                        placeholder="e.g. Dinas Komunikasi dan Informatika" />
                    @error('name')
                        <x-core::input-error :message="$message" />
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <x-core::label for="slug" :value="__('Slug *')" />
                    <x-core::input id="slug" type="text" wire:model="slug" required placeholder="dinas-kominfo" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Unique identifier for the organization') }}</p>
                    @error('slug')
                        <x-core::input-error :message="$message" />
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('rakaca.landlord.organization.index') }}" wire:navigate>
                        <x-core::secondary-button type="button">
                            {{ __('Cancel') }}
                        </x-core::secondary-button>
                    </a>
                    <x-core::button type="submit">
                        {{ __('Update Organization') }}
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>