<div>
    <div class="relative overflow-hidden bg-white border border-gray-200 shadow-sm dark:bg-gray-900 rounded-2xl dark:border-gray-700/60">

        {{-- Progress Bar --}}
        <div wire:loading
             wire:target="sort,nextPage,previousPage,gotoPage,updatedPerPage,deleteBaleUser,query"
             class="absolute top-0 inset-x-0 z-20 h-[3px] overflow-hidden rounded-t-2xl">
            <div class="h-full w-full bg-linear-to-r from-indigo-500 via-purple-500 to-indigo-500 bg-size-[200%_100%] animate-shimmer"></div>
        </div>

        {{-- Toolbar --}}
        <div class="px-4 py-4 border-b border-gray-100 dark:border-gray-700/60 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div></div>
                <div class="flex items-center gap-2 sm:gap-3 flex-wrap sm:flex-nowrap">
                    {{-- Search --}}
                    <div class="relative flex-1 sm:flex-none sm:min-w-56">
                        <div wire:loading.flex wire:target="query"
                            class="absolute inset-y-0 start-0 items-center ps-3 pointer-events-none z-10">
                            <svg class="size-4 animate-spin text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                                <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                        <div wire:loading.remove wire:target="query"
                            class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="size-4 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        @if($query)
                            <button type="button" wire:click="$set('query', '')"
                                class="absolute inset-y-0 end-0 flex items-center pe-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors z-10">
                                <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 6 6 18M6 6l12 12"/></svg>
                            </button>
                        @endif
                        <input type="text" wire:model.live.debounce.400ms="query"
                            placeholder="{{ __('Search by name, email, bale, or role...') }}"
                            class="block w-full py-2 ps-9 {{ $query ? 'pe-8' : 'pe-4' }} text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:placeholder-gray-500 dark:focus:border-indigo-500 dark:focus:ring-indigo-900/30" />
                    </div>

                    {{-- Per-page --}}
                    <div class="relative">
                        <select wire:model.live="perPage"
                            class="appearance-none py-2 ps-3 pe-8 text-sm bg-gray-50 border border-gray-200 rounded-xl text-gray-700 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 focus:outline-none transition-all dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:focus:border-indigo-500 cursor-pointer">
                            <option value="10">10 / page</option>
                            <option value="20">20 / page</option>
                            <option value="50">50 / page</option>
                            <option value="100">100 / page</option>
                        </select>
                        <div class="absolute inset-y-0 end-2 flex items-center pointer-events-none">
                            <svg class="size-3.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 9 6 6 6-6"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700/60">
                <thead class="bg-gray-50/70 dark:bg-gray-800/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('User') }}</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Bale Assignments') }}</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody
                    wire:loading.class="opacity-40 pointer-events-none select-none"
                    wire:target="sort,query,deleteBaleUser"
                    class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-900 transition-opacity duration-200">
                    @forelse($users as $record)
                        @include('rakaca::livewire.pages.landlord.bale-user.section.bale-user-row')
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    @if($query)
                                        <div class="size-14 rounded-full bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                                            <svg class="size-7 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M11 8v4M11 16h.01"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('No results found') }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('No users matched') }} "<span class="font-medium text-gray-600 dark:text-gray-400">{{ $query }}</span>"</p>
                                        </div>
                                        <button type="button" wire:click="$set('query', '')" class="mt-1 text-xs font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 hover:underline transition-colors">{{ __('Clear search') }}</button>
                                    @else
                                        <div class="size-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                            <svg class="size-7 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('No data yet') }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Get started by adding your first bale user.') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->total() > 0)
            <div class="px-4 py-3.5 border-t border-gray-100 dark:border-gray-700/60 sm:px-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Showing') }}
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $users->firstItem() }}</span>
                        &ndash;
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $users->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ number_format($users->total()) }}</span>
                        {{ __('users') }}
                        @if($query)
                            <span class="text-indigo-500 font-medium ml-1">{{ __('for') }} "<em>{{ $query }}</em>"</span>
                        @endif
                    </p>

                    <div class="flex items-center gap-1">
                        @if($users->onFirstPage())
                            <span class="inline-flex items-center justify-center size-8 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg></span>
                        @else
                            <button wire:click="previousPage" type="button" class="inline-flex items-center justify-center size-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 transition-all"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg></button>
                        @endif

                        @php
                            $currentPage = $users->currentPage();
                            $lastPage    = $users->lastPage();
                            $from        = max(1, $currentPage - 2);
                            $to          = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($from > 1)
                            <button wire:click="gotoPage(1)" type="button" class="inline-flex items-center justify-center size-8 rounded-lg text-xs font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 transition-all">1</button>
                            @if($from > 2)<span class="inline-flex items-center justify-center size-8 text-xs text-gray-400">...</span>@endif
                        @endif

                        @for($i = $from; $i <= $to; $i++)
                            @if($i === $currentPage)
                                <span class="inline-flex items-center justify-center size-8 rounded-lg text-xs font-semibold bg-indigo-600 text-white shadow-sm">{{ $i }}</span>
                            @else
                                <button wire:click="gotoPage({{ $i }})" type="button" class="inline-flex items-center justify-center size-8 rounded-lg text-xs font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 transition-all">{{ $i }}</button>
                            @endif
                        @endfor

                        @if($to < $lastPage)
                            @if($to < $lastPage - 1)<span class="inline-flex items-center justify-center size-8 text-xs text-gray-400">...</span>@endif
                            <button wire:click="gotoPage({{ $lastPage }})" type="button" class="inline-flex items-center justify-center size-8 rounded-lg text-xs font-medium text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 transition-all">{{ $lastPage }}</button>
                        @endif

                        @if($users->hasMorePages())
                            <button wire:click="nextPage" type="button" class="inline-flex items-center justify-center size-8 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:text-gray-400 dark:hover:text-indigo-400 dark:hover:bg-indigo-900/20 transition-all"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></button>
                        @else
                            <span class="inline-flex items-center justify-center size-8 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed"><svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg></span>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
