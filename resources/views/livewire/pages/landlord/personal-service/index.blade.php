<div>
    <x-core::page-header gradient :title="__('Personal Service Management')" :subtitle="__('Manage customer services')">
        <x-slot name="action">
            @can('personal-service.create')
                <a href="{{ route('rakaca.landlord.personal-service.create') }}" wire:navigate
                    class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <x-lucide-user-plus class="w-5 h-5" />
                    {{ __('Add Customer') }}
                </a>
            @endcan
        </x-slot>
    </x-core::page-header>

    {{-- Search --}}
    <div class="mb-4">
        <div class="relative max-w-sm">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <x-lucide-search class="w-4 h-4 text-slate-400" />
            </div>
            <input type="text" wire:model.live.debounce.400ms="query"
                class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                placeholder="{{ __('Search by name, email, or service...') }}">
        </div>
    </div>

    {{-- Flash Message --}}
    {{-- @if(session()->has('message'))
    <div
        class="mb-4 p-4 rounded-xl bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800 text-sm text-teal-800 dark:text-teal-400">
        {{ session('message') }}
    </div>
    @endif --}}

    {{-- Customer List --}}
    <div class="space-y-3">
        @forelse($this->customers as $customer)
            <div wire:key="customer-{{ $customer['uuid'] }}"
                class="bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all hover:shadow-md">
                {{-- User Header --}}
                <div
                    class="flex items-center justify-between px-5 py-4 border-b border-gray-50 dark:border-slate-800 bg-linear-to-r from-slate-50/80 to-transparent dark:from-slate-800/50">
                    <div class="flex items-center gap-3">
                        {{-- Avatar --}}
                        <div
                            class="shrink-0 w-10 h-10 rounded-full bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow">
                            <span class="text-sm font-bold text-white">
                                {{ strtoupper(substr($customer['user']?->name ?? '?', 0, 1)) }}
                            </span>
                        </div>
                        {{-- User Info --}}
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $customer['user']?->name ?? __('Unknown User') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $customer['user']?->email ?? $customer['uuid'] }}
                            </p>
                        </div>
                    </div>

                    {{-- Active count badge & Add service button --}}
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <x-lucide-layers class="w-3 h-3" />
                            {{ $customer['active_count'] }} {{ __('Active') }}
                        </span>

                        @can('personal-service.create')
                            <a href="{{ route('rakaca.landlord.personal-service.create', ['user_uuid' => $customer['uuid']]) }}"
                                wire:navigate
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/40 transition-colors border border-indigo-200 dark:border-indigo-800">
                                <x-lucide-plus class="w-3.5 h-3.5" />
                                {{ __('Add Service') }}
                            </a>
                        @endcan

                        @can('personal-service.delete')
                            <button
                                x-on:click="if(confirm('{{ __('Are you sure you want to delete this customer?') }}')) $wire.dispatch('deleteCustomer', { uuid: '{{ $customer['uuid'] }}' })"
                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors border border-red-200 dark:border-red-800">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                {{ __('Delete All') }}
                            </button>
                        @endcan
                    </div>
                </div>

                {{-- Services --}}
                <div class="px-5 py-3">
                    @if($customer['services']->isEmpty())
                        <p class="text-xs text-gray-400 dark:text-gray-500 italic">{{ __('No services assigned') }}</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($customer['services'] as $ps)
                                    <div wire:key="ps-{{ $ps->id }}"
                                        class="group flex items-center gap-1.5 py-1 px-3 rounded-full text-xs font-medium border transition-all
                                                                                                    {{ $ps->actived
                                ? 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 border-teal-200 dark:border-teal-800'
                                : 'bg-gray-50 dark:bg-slate-800 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-slate-700' }}">
                                        {{-- Status dot --}}
                                        <span
                                            class="{{ $ps->actived ? 'bg-teal-500' : 'bg-gray-400' }} w-1.5 h-1.5 rounded-full shrink-0"></span>
                                        {{ $ps->service?->name ?? 'N/A' }}

                                        {{-- Actions --}}
                                        <div class="hidden group-hover:flex items-center gap-1 ml-1">
                                            @can('personal-service.update')
                                                <a href="{{ route('rakaca.landlord.personal-service.edit', $ps->id) }}" wire:navigate
                                                    class="text-indigo-500 hover:text-indigo-700 transition-colors">
                                                    <x-lucide-pencil class="w-3 h-3" />
                                                </a>
                                            @endcan
                                            @can('personal-service.delete')
                                                <button
                                                    x-on:click="if(confirm('{{ __('Remove this service from customer?') }}')) $wire.dispatch('deleteService', { id: '{{ $ps->id }}' })"
                                                    class="text-red-500 hover:text-red-700 transition-colors">
                                                    <x-lucide-x class="w-3 h-3" />
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div
                class="text-center py-16 bg-white dark:bg-slate-900 rounded-xl border border-gray-100 dark:border-slate-800">
                <x-lucide-users class="w-12 h-12 mx-auto text-gray-300 dark:text-slate-600 mb-3" />
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('No customers found') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Add a new customer to get started') }}</p>
                @can('personal-service.create')
                    <a href="{{ route('rakaca.landlord.personal-service.create') }}" wire:navigate
                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-linear-to-r from-indigo-500 to-purple-600 rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all">
                        <x-lucide-plus class="w-4 h-4" />
                        {{ __('Add Customer') }}
                    </a>
                @endcan
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($this->customers->hasPages())
        <div class="mt-6">
            {{ $this->customers->links() }}
        </div>
    @endif
</div>