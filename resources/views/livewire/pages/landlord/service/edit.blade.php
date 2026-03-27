<div>
    <x-core::breadcrumb :items="[
        ['label' => 'Service Management', 'route' => 'rakaca.landlord.service.index'],
    ]" active="Edit Service" />

    <x-core::page-header title="Edit Service" subtitle="Perbarui informasi layanan Rakaca" />

    <div class="max-w-3xl">
        <div class="p-6 bg-white border border-gray-200 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
            <form wire:submit="save" class="space-y-6">
                {{-- Name --}}
                <div>
                    <x-core::label for="name" value="Service Name *" />
                    <x-core::input id="name" type="text" wire:model.live="name" required
                        placeholder="e.g. Internet Desa" />
                    @error('name')
                        <x-core::input-error :message="$message" />
                    @enderror
                </div>

                {{-- Slug --}}
                <div>
                    <x-core::label for="slug" value="Slug *" />
                    <x-core::input id="slug" type="text" wire:model="slug" required placeholder="internet-desa" />
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unique identifier for the service</p>
                    @error('slug')
                        <x-core::input-error :message="$message" />
                    @enderror
                </div>

                {{-- Icon --}}
                <div>
                    <x-core::label for="icon" value="Icon (Lucide Name)" />
                    <div class="flex gap-x-3 items-center">
                        <x-core::input id="icon" type="text" wire:model.live="icon"
                            placeholder="e.g. globe, activity, users" />
                        <div
                            class="shrink-0 w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700">
                            @if($icon)
                                <x-dynamic-component :component="'lucide-' . $icon"
                                    class="w-5 h-5 text-slate-600 dark:text-slate-400" />
                            @else
                                <x-lucide-help-circle class="w-5 h-5 text-slate-400" />
                            @endif
                        </div>
                    </div>
                    @error('icon')
                        <x-core::input-error :message="$message" />
                    @enderror
                </div>

                {{-- Actived --}}
                <div class="flex items-center gap-x-3">
                    <input id="actived" type="checkbox" wire:model="actived"
                        class="shrink-0 mt-0.5 border-gray-200 rounded text-indigo-600 focus:ring-indigo-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:checked:bg-indigo-500 dark:checked:border-indigo-500 dark:focus:ring-offset-gray-800">
                    <x-core::label for="actived" value="Active Status" class="mb-0" />
                    @error('actived')
                        <x-core::input-error :message="$message" />
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('rakaca.landlord.service.index') }}" wire:navigate>
                        <x-core::secondary-button type="button">
                            Cancel
                        </x-core::secondary-button>
                    </a>
                    <x-core::button type="submit">
                        Update Service
                    </x-core::button>
                </div>
            </form>
        </div>
    </div>
</div>