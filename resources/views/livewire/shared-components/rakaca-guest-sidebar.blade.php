<div>
    {{-- Sidebar --}}
    <div id="application-sidebar"
        class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform hidden fixed top-0 left-0 bottom-0 z-60 lg:z-50 w-64 overflow-y-auto scrollbar-thin scrollbar-thumb-slate-700 scrollbar-track-transparent lg:block lg:translate-x-0 lg:right-auto lg:bottom-0
               bg-linear-to-b from-slate-900 via-slate-900 to-slate-800
               border-r border-slate-700/60">

        @persist('sidebar-rakaca-guest')

        {{-- ========== User Context Card ========== --}}
        <div class="px-4 pt-6 pb-4">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-sm group">
                {{-- Avatar / User Icon --}}
                <div class="shrink-0 w-10 h-10 rounded-lg bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <x-lucide-user class="w-5 h-5 text-white" />
                </div>

                {{-- User Name --}}
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest leading-none mb-1">
                        {{ __('Personal') }}
                    </p>
                    <p class="text-sm font-semibold text-white truncate leading-snug">
                        {{ auth()->user()->name ?? __('Guest') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ========== Main Navigation ========== --}}
        <div class="px-4 mb-2 mt-4">
            <div class="flex items-center gap-2">
                <div class="h-px flex-1 bg-slate-700/60"></div>
                <span class="text-[10px] uppercase tracking-widest text-slate-500 font-semibold text-center">{{ __('Services') }}</span>
                <div class="h-px flex-1 bg-slate-700/60"></div>
            </div>
        </div>

        <nav class="flex flex-col w-full px-3 pb-10" data-hs-accordion-always-open>
            <ul class="space-y-0.5">
                @foreach ($this->availableMenus as $menu)
                    <li>
                        <a href="/{{ $menu['url'] }}" wire:navigate.hover
                            class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                                   text-slate-400 hover:text-white hover:bg-white/8
                                   transition-all duration-150 ease-in-out"
                            wire:current="bg-indigo-600/25 border border-indigo-500/40 text-white shadow-xs">

                            {{-- Icon --}}
                            <span class="shrink-0 w-5 h-5 text-slate-500 group-hover:text-indigo-400 transition-colors duration-150">
                                <x-dynamic-component :component="'lucide-' . ($menu['icon'] ?? 'circle')" class="w-5 h-5" />
                            </span>

                            {{-- Label --}}
                            <span class="tracking-wide">{{ $menu['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

        @endpersist
    </div>
    {{-- End Sidebar --}}
</div>