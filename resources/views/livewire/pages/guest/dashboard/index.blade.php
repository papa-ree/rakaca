<div>
    {{-- Welcome Banner --}}
    <div class="relative overflow-hidden p-8 mb-8 text-white rounded-2xl shadow-xl"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);" data-aos="fade-up">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-6 md:mb-0">
                <h1 class="text-3xl font-bold text-white md:text-4xl mb-3">Selamat datang di Rakaca!</h1>
                <p class="max-w-3xl text-white/90 text-lg mb-2">
                    Rakaca (Rak Kaca) adalah etalase layanan TIK yang dimiliki oleh Dinas Kominfo dan Statistik
                    Kabupaten Ponorogo.
                </p>
                <p class="max-w-3xl text-white/80">
                    Kami menyediakan layanan TIK untuk memenuhi kebutuhan instansi Pemerintah Kabupaten Ponorogo.
                </p>
            </div>
            <div class="shrink-0">
                <div class="p-4 bg-white/20 backdrop-blur-md rounded-2xl">
                    <x-lucide-award class="w-16 h-16 text-white" />
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Section --}}
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 lg:grid-cols-4" data-aos="fade-up" data-aos-delay="100">
        {{-- Active Services Card --}}
        <livewire:rakaca.pages.guest.dashboard.section.active-service-card />

        {{-- Total Services Available --}}
        <div
            class="group p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl hover:shadow-xl dark:border-gray-700 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-linear-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg">
                    <x-lucide-layers class="w-6 h-6 text-white" />
                </div>
                <span
                    class="px-3 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full dark:bg-purple-900/50 dark:text-purple-300">Tersedia</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Layanan</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">9</p>
            </div>
            <div class="mt-4">
                <a href="#services"
                    class="inline-flex items-center text-xs font-medium text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                    Lihat semua layanan
                    <x-lucide-arrow-right class="w-3 h-3 ml-1" />
                </a>
            </div>
        </div>

        {{-- Pending Submissions --}}
        <div
            class="group p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl hover:shadow-xl dark:border-gray-700 hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-linear-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg">
                    <x-lucide-clock class="w-6 h-6 text-white" />
                </div>
                <span
                    class="px-3 py-1 text-xs font-semibold text-amber-700 bg-amber-100 rounded-full dark:bg-amber-900/50 dark:text-amber-300">Menunggu</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pengajuan Pending</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">0</p>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada pengajuan menunggu</p>
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
                    class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">Online</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Akun</p>
                <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">Guest</p>
            </div>
            <div class="mt-4">
                <p class="text-xs text-gray-500 dark:text-gray-400">Aktif sekarang</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3" data-aos="fade-up" data-aos-delay="150">
        <a href="#services"
            class="group p-6 bg-linear-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 border border-blue-200 dark:border-blue-800 rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-600 rounded-xl">
                    <x-lucide-badge-plus class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Aktivasi Layanan</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pilih layanan baru</p>
                </div>
            </div>
        </a>

        <a href="#submissions"
            class="group p-6 bg-linear-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 border border-purple-200 dark:border-purple-800 rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-600 rounded-xl">
                    <x-lucide-file-text class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Lihat Pengajuan</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Status pengajuan Anda</p>
                </div>
            </div>
        </a>

        <a href="https://wa.me/6285126061182" target="_blank"
            class="group p-6 bg-linear-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 border border-green-200 dark:border-green-800 rounded-2xl hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-600 rounded-xl">
                    <x-lucide-headphones class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">Bantuan & Support</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Hubungi kami</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Submission Status Section --}}
    <div id="submissions" class="mb-8" data-aos="fade-up" data-aos-delay="200">
        <div class="p-6 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl dark:border-gray-700">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Status Pengajuan</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Pantau status pengajuan layanan Anda</p>
                </div>
                <div class="p-3 bg-linear-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg">
                    <x-lucide-clipboard-list class="w-6 h-6 text-white" />
                </div>
            </div>

            {{-- Empty State --}}
            <div class="text-center py-12">
                <div
                    class="flex items-center justify-center w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700">
                    <x-lucide-inbox class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                </div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Pengajuan</h4>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Anda belum memiliki pengajuan layanan. Mulai dengan memilih layanan yang Anda butuhkan.
                </p>
                <a href="#services">
                    <x-core::button label="Mulai Pengajuan" type="button" class="inline-flex items-center gap-x-2">
                        <x-slot name="icon">
                            <x-lucide-plus class="w-5 h-5" />
                        </x-slot>
                    </x-core::button>
                </a>
            </div>

            {{-- Example of filled state (commented out for reference) --}}
            <div class="space-y-3">
                <div
                    class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                                <x-lucide-server class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">VPS Server</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pengajuan #12345</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 text-xs font-semibold text-amber-700 bg-amber-100 rounded-full dark:bg-amber-900/50 dark:text-amber-300">
                            Dalam Review
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Diajukan: 31 Des 2025</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Lihat Detail</a>
                    </div>
                </div>

                <div
                    class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg">
                                <x-lucide-mail class="w-5 h-5 text-green-600 dark:text-green-400" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Email Pemerintah</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Pengajuan #12344</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full dark:bg-green-900/50 dark:text-green-300">
                            Disetujui
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Disetujui: 30 Des 2025</span>
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA Section --}}
    <div class="p-8 mb-8 bg-linear-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl"
        data-aos="fade-up" data-aos-delay="250">
        <div class="max-w-3xl mx-auto text-center">
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-full bg-linear-to-br from-emerald-500 to-teal-500 shadow-lg">
                <x-lucide-zap class="w-10 h-10 text-white" />
            </div>
            <h2 class="mb-3 text-3xl font-bold text-gray-900 dark:text-white">Siap Untuk Memulai?</h2>
            <p class="mb-8 text-lg text-gray-600 dark:text-gray-300">
                Pilih layanan yang sesuai dengan kebutuhan instansi Anda dan nikmati dukungan TIK profesional dari kami.
            </p>

            <a href="#services">
                <x-core::button label="Jelajahi Layanan" type="button"
                    class="inline-flex items-center gap-x-3 px-8 py-3 text-lg font-semibold">
                    <x-slot name="icon">
                        <x-lucide-rocket class="w-6 h-6" />
                    </x-slot>
                </x-core::button>
            </a>
        </div>
    </div>

    {{-- Service List --}}
    <livewire:rakaca.pages.guest.dashboard.section.service-list />
</div>