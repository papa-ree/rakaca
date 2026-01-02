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

        {{-- Pending Submissions --}}
        <livewire:rakaca.pages.guest.dashboard.section.pending-submission-card />

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
    <livewire:rakaca.pages.guest.dashboard.section.submission-status />

    {{-- CTA Section --}}
    <div class="p-8 mb-8 bg-linear-to-br from-purple-50 to-purple-50 dark:from-purple-900/20 dark:to-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-2xl"
        data-aos="fade-up" data-aos-delay="250">
        <div class="max-w-3xl mx-auto text-center">
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-full bg-linear-to-br from-purple-500 to-purple-500 shadow-lg">
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