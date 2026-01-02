<div>
    {{-- Hero Section --}}
    <div class="relative overflow-hidden p-8 mb-8 text-white rounded-2xl shadow-xl"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);" data-aos="fade-up">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>

        <div class="relative z-10 max-w-5xl mx-auto text-center">
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-full bg-white/20 backdrop-blur-md">
                <x-lucide-building-2 class="w-10 h-10 text-white" />
            </div>
            <h1 class="text-3xl font-bold text-white md:text-4xl mb-3">Pilih Layanan Bale Anda</h1>
            <p class="max-w-2xl mx-auto text-white/90 text-lg">
                Silakan pilih salah satu layanan di bawah ini untuk mulai mengelola konten dan website Anda.
            </p>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2" data-aos="fade-up" data-aos-delay="100">
        {{-- Total Bales Card --}}
        <div
            class="group p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl hover:shadow-xl dark:border-gray-700 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-linear-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
                    <x-lucide-building class="w-6 h-6 text-white" />
                </div>
                <span
                    class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900/50 dark:text-blue-300">Tersedia</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Layanan Bale</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ count($bales) }}</p>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($bales) > 0 ? 'Pilih untuk memulai' : 'Tidak ada layanan tersedia' }}
                </p>
            </div>
        </div>

        {{-- Account Status Card --}}
        <div
            class="group p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl hover:shadow-xl dark:border-gray-700 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-linear-to-br from-green-500 to-green-600 rounded-xl shadow-lg">
                    <x-lucide-user-check class="w-6 h-6 text-white" />
                </div>
                <span
                    class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">Aktif</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Akun</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">Verified</p>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Akses penuh ke semua layanan</p>
            </div>
        </div>
    </div>

    {{-- Bale Selection Cards --}}
    @if(count($bales) > 0)
        <div class="mb-8" data-aos="fade-up" data-aos-delay="150">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Layanan Tersedia</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Klik untuk masuk ke dashboard layanan</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($bales as $bale)
                    <button wire:click="selectBale('{{ $bale->id }}')" wire:loading.attr="disabled" wire:target="selectBale('{{ $bale->id }}')"
                        class="group relative cursor-pointer flex flex-col p-6 text-left transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 dark:border-gray-700 rounded-2xl hover:shadow-xl hover:-translate-y-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 dark:focus:ring-purple-400 dark:focus:ring-offset-gray-900">

                        {{-- Gradient Accent --}}
                        <div
                            class="absolute top-0 left-0 right-0 h-1 bg-linear-to-r from-purple-500 via-pink-500 to-blue-500 rounded-t-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        </div>

                        <div class="flex items-start justify-between mb-4">
                            <div
                                class="flex items-center justify-center w-14 h-14 bg-linear-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg transition-transform duration-200 group-hover:scale-110">
                                <x-lucide-building-2 class="w-7 h-7 text-white" />
                            </div>
                            <span
                                class="px-2 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-lg dark:bg-purple-900/50 dark:text-purple-300 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                Aktif
                            </span>
                        </div>

                        <h3
                            class="text-xl font-bold text-gray-900 transition-colors dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 mb-2">
                            {{ $bale->name }}
                        </h3>

                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                            {{ $bale->slug }}
                        </p>

                        {{-- Divider --}}
                        <div class="h-px bg-gray-200 dark:bg-gray-700 mb-4"></div>

                        {{-- Action Text --}}
                        <div class="justify-between items-center flex">
                            <div
                                class="flex items-center text-sm font-semibold text-purple-600 dark:text-purple-400 opacity-0 transform translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                                <x-lucide-arrow-right class="w-4 h-4 mr-2 transition-transform group-hover:translate-x-1" />
                                Masuk Dashboard
                            </div>
                            <div wire:loading wire:target="selectBale('{{ $bale->id }}')"
                            class="flex items-center text-sm font-semibold text-purple-600 dark:text-purple-400 opacity-0 transform translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                                <svg class="w-8 h-8 absolute -bottom-4 -right-1 text-purple-600 animate-spin" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if(count($bales) === 0)
        <div class="mb-8" data-aos="fade-up" data-aos-delay="150">
            <div class="p-12 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl dark:border-gray-700">
                <div class="text-center">
                    <div
                        class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-full bg-linear-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600">
                        <x-lucide-inbox class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Tidak Ada Layanan Tersedia</h3>
                    <p class="max-w-md mx-auto text-gray-600 dark:text-gray-400 mb-6">
                        Anda belum memiliki akses ke layanan Bale manapun saat ini. Silakan hubungi administrator untuk
                        mendapatkan akses.
                    </p>

                    {{-- Quick Actions --}}
                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
                        <a href="{{ route('rakaca.guest.dashboard') }}"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                            <x-lucide-arrow-left class="w-4 h-4 mr-2" />
                            Kembali ke Dashboard
                        </a>
                        <a href="https://wa.me/6285126061182" target="_blank"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-semibold text-white bg-linear-to-br from-green-500 to-green-600 rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-lg hover:shadow-xl">
                            <x-lucide-headphones class="w-4 h-4 mr-2" />
                            Hubungi Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Help Section --}}
    <div class="p-6 bg-linear-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl"
        data-aos="fade-up" data-aos-delay="200">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-blue-600 rounded-xl shrink-0">
                <x-lucide-info class="w-5 h-5 text-white" />
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">Butuh Bantuan?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Jika Anda mengalami kesulitan atau memerlukan bantuan dalam memilih layanan, tim support kami siap
                    membantu Anda.
                </p>
                <a href="https://wa.me/6285126061182" target="_blank"
                    class="inline-flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                    Hubungi Support
                    <x-lucide-external-link class="w-4 h-4 ml-1" />
                </a>
            </div>
        </div>
    </div>
</div>