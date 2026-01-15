<div>
    {{-- <!-- Sidebar --> --}}
    <div id="application-sidebar"
        class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform hidden fixed top-0 left-0 bottom-0 z-60 lg:z-50 w-64 bg-white border-r border-gray-200 pt-7 pb-10 overflow-y-auto scrollbar-y lg:block lg:translate-x-0 lg:right-auto lg:bottom-0 dark:scrollbar-y dark:bg-gray-800 dark:border-gray-700">

        @persist('sidebar-rakaca')
        <nav class="flex flex-col flex-wrap w-full p-6 space-y-6" data-hs-accordion-always-open>
            <ul class="space-y-1.5 hs-accordion-group">

                @foreach ($this->availableMenus as $menu)
                    <li>
                        <a href="/{{$menu['url']}}" wire:navigate.hover wire:navigate.hover
                            class="flex capitalize items-center gap-x-3.5 py-2 px-2.5 text-sm text-slate-700 rounded-md hover:bg-gray-200 hover:text-slate-800 duration-200 ease-in-out transition-all hover:dark:bg-gray-900 dark:text-white"
                            wire:current="bg-gray-100 dark:bg-gray-900">{{ $menu['label'] }}</a>
                    </li>
                @endforeach

            </ul>

            <ul>
                <li>
                    <h5 class="mb-3 text-sm font-semibold text-gray-800 dark:text-gray-200">Data Internet Desa</h5>
                    <ul class="ms-0.5 space-y-2 border-s-2 border-gray-100 dark:border-gray-800">
                        @if ($this->availableKosadataMenus == [])
                            <div class="dark:text-white text-xs">
                                <p>{{ 'Please run:' }}</p>
                                <p>{{ 'composer require nawasara/kosadata' }}</p>
                            </div>
                        @endif

                        @foreach ($this->availableKosadataMenus as $menu)
                            <li>
                                <a class="block py-1 ps-4 -ms-px border-s-2 border-transparent text-sm text-gray-700 hover:border-gray-400 hover:text-gray-900 focus:outline-hidden focus:border-gray-400 focus:text-gray-900 dark:text-gray-400 dark:hover:text-gray-300 dark:focus:text-gray-300 "
                                    href="/{{$menu['url']}}" wire:navigate.hover
                                    wire:current="bg-gray-100 dark:bg-gray-900 text-blue-600 dark:text-blue-500 border-blue-600">{{ $menu['label'] }}</a>
                            </li>
                        @endforeach

                    </ul>
                </li>
            </ul>
        </nav>
        @endpersist

    </div>
    {{-- <!-- End Sidebar --> --}}
</div>