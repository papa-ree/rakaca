<div>
    {{-- <!-- Sticky Navbar --> --}}
    <header class="sticky top-0 z-50">
        <nav class="border-b border-gray-200 nav-blur bg-white/80 dark:bg-gray-800/80 dark:border-gray-700">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex">
                            <span
                                class="text-xl font-bold text-transparent bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text">
                                Rakaca
                            </span>
                        </div>
                        <div class="hidden md:block">
                            <div class="flex items-baseline ml-10 space-x-4">
                                <a href="#Layanan"
                                    class="px-3 py-2 text-sm font-medium transition rounded-md hover:text-primary-600 dark:hover:text-primary-400">Layanan</a>
                                <a href="#Support"
                                    class="px-3 py-2 text-sm font-medium transition rounded-md hover:text-primary-600 dark:hover:text-primary-400">Support</a>
                                <a href="#Bantuan"
                                    class="px-3 py-2 text-sm font-medium transition rounded-md hover:text-primary-600 dark:hover:text-primary-400">Bantuan</a>
                                <a href="#Kontak"
                                    class="px-3 py-2 text-sm font-medium transition rounded-md hover:text-primary-600 dark:hover:text-primary-400">Kontak</a>
                                <a href="/form-internet-desa" wire:navigate.hover
                                    class="px-3 py-2 text-sm font-medium transition rounded-md hover:text-primary-600 dark:hover:text-primary-400">Form
                                    Internet Desa
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <button @click="darkMode = !darkMode" class="p-2 rounded-full focus:outline-none">
                            <span x-show="!darkMode" class="text-gray-700">
                                <i class="fas fa-moon"></i>
                            </span>
                            <span x-show="darkMode" class="text-yellow-300">
                                <i class="fas fa-sun"></i>
                            </span>
                        </button>
                        <button
                            class="inline-flex items-center justify-center p-2 ml-4 rounded-md md:hidden focus:outline-none">
                            <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="py-20 text-white md:py-32 relative overflow-hidden"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-48 -mt-48"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-white/10 rounded-full -ml-32 -mb-32"></div>

        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="mb-6 text-4xl font-bold md:text-5xl lg:text-6xl">
                    Etalase Layanan Teknologi Informasi dan Infrastruktur
                </h1>
                <p class="max-w-3xl mx-auto text-3xl font-bold md:text-4xl opacity-90">
                    Bidang Aplikasi dan Informatika
                </p>
                <p class="max-w-3xl mx-auto mb-8 text-3xl font-bold md:text-4xl opacity-90">
                    Dinas Kominfo dan Statistik
                </p>
                <p class="max-w-3xl mx-auto mb-8 text-xl md:text-2xl opacity-90">
                    Solusi digital yang andal untuk mendukung Instansi Pemerintah Kabupaten Ponorogo
                </p>
                <a href="#Layanan"
                    class="inline-flex items-center px-8 py-3 text-base font-medium transition duration-300 bg-white border border-transparent rounded-md shadow-sm text-purple-700 hover:bg-gray-100 md:py-4 md:text-lg md:px-10">
                    Eksplorasi Layanan
                </a>
            </div>
        </div>
    </section>

    {{-- <!-- Layanan Catalog Section --> --}}
    <section id="Layanan" class="py-16 bg-white md:py-24 dark:bg-gray-800">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold md:text-4xl">Katalog Layanan TI Kami</h2>
                <p class="max-w-3xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                    Solusi digital yang komprehensif disesuaikan untuk memenuhi kebutuhan organisasi
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">


                {{-- Jaringan --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-network-wired"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Layanan Jaringan</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Penyiapan infrastruktur jaringan profesional termasuk solusi kabel dan nirkabel.
                    </p>
                </div>

                {{-- Email --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-envelope"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Layanan Email</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Pembuatan email profesional, konfigurasi, dan pemulihan akun Layanan untuk organisasi Anda.
                    </p>
                </div>


                {{-- Subdomain --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-globe"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Subdomain</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Layanan penyiapan, pengelolaan, dan pemulihan subdomain yang aman untuk aplikasi web Anda.
                    </p>
                </div>

                {{-- Hosting --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-cloud"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Hosting</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Layanan penyiapan, pengelolaan dan pemulihan hosting yang aman untuk aplikasi web Anda.
                    </p>
                </div>

                {{-- Hosting --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-server"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">VPS</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Solusi server privat untuk aplikasi khusus anda.
                    </p>
                </div>


                {{-- Aplikasi --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-code"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Pembangunan Aplikasi</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Pengembangan aplikasi yang disesuaikan dengan kebutuhan spesifik Anda.
                    </p>
                </div>

                <!-- Service Card 3 -->
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-file-alt"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Content Management</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Solusi CMS yang komprehensif termasuk layanan pengaturan, penyesuaian, dan migrasi konten.
                    </p>
                </div>

                {{-- Virtual Meeting --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-video"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Dukungan Rapat Online</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Solusi konferensi video yang aman dengan bantuan pengaturan dan dukungan teknis.
                    </p>
                </div>

                {{-- Notification Gateway --}}
                <div class="p-6 shadow-sm card-hover bg-gray-50 dark:bg-gray-700 rounded-xl hover:shadow-md">
                    <div
                        class="mb-4 feature-icon bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400">
                        <i class="text-2xl fas fa-bolt"></i>
                    </div>
                    <h3 class="mb-2 text-xl font-semibold">Notification Gateway</h3>
                    <p class="text-gray-600 dark:text-gray-300">
                        Integrasi peringatan instan dengan aplikasi untuk kebutuhan organisasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- <!-- Support & Infrastructure Section --> --}}
    <section id="Support" class="py-16 md:py-24 bg-gray-50 dark:bg-gray-900">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="mb-4 text-3xl font-bold md:text-4xl">Support Kami</h2>
                <p class="max-w-3xl mx-auto text-lg text-gray-600 dark:text-gray-300">
                    Support optimum ke dalam setiap layanan
                </p>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">

                {{-- SSO --}}
                <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-user-shield"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">Single Sign-On (SSO)</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Otentikasi terpusat di seluruh layanan dengan manajemen identitas yang aman.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- <!-- Feature 2 --> --}}
                {{-- <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">Kontrol Akses Berbasis Role dan Permission</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Izin terperinci dengan pemisahan pengguna untuk akses data yang aman.
                            </p>
                        </div>
                    </div>
                </div> --}}

                {{-- <!-- Feature 3 --> --}}
                {{-- <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-lock"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">Akses Jarak Jauh yang Aman</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Solusi tunneling terenkripsi untuk koneksi jarak jauh yang terlindungi.
                            </p>
                        </div>
                    </div>
                </div> --}}

                {{-- <!-- Feature 4 --> --}}
                <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">HTTPS/SSL Enabled</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Semua layanan dilengkapi dengan koneksi terenkripsi secara default.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- MFA --}}
                <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-fingerprint"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">Otentikasi Multi-Faktor</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Lapisan keamanan tambahan yang melindungi akun Anda
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Activity Alert --}}
                <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-tv-alt"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">Layanan yang termonitor</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Peringatan suntuk kejadian Keamanan dan aktivitas sistem.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Multi Tenant --}}
                {{-- <div class="p-6 bg-white shadow-sm dark:bg-gray-800 rounded-xl">
                    <div class="flex items-start">
                        <div
                            class="mr-4 feature-icon bg-secondary-100 dark:bg-secondary-900/30 text-secondary-600 dark:text-secondary-400">
                            <i class="text-xl fas fa-project-diagram"></i>
                        </div>
                        <div>
                            <h3 class="mb-2 text-xl font-semibold">Multi-Tenant Support</h3>
                            <p class="text-gray-600 dark:text-gray-300">
                                Solusi penyimpanan dan data terisolasi untuk berbagai unit organisasi.
                            </p>
                        </div>
                    </div>
                </div> --}}
                {{--
            </div> --}}

        </div>
    </section>

    {{-- Helpdesk Section --}}
    <section class="py-16 text-white md:py-24 bg-primary-600 dark:bg-primary-800">
        <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
            <h2 class="mb-6 text-3xl font-bold md:text-4xl">
                Mengalami Kendala Layanan?
            </h2>
            <p class="max-w-3xl mx-auto mb-8 text-xl opacity-90">
                Tim kami siap membantu Anda.
            </p>
            <a href="/bantuan"
                class="inline-flex items-center px-8 py-3 text-base font-medium transition duration-300 bg-white border border-transparent rounded-md shadow-sm text-primary-600 hover:bg-gray-100 md:py-4 md:text-lg md:px-10">
                Bantuan
            </a>
            {{-- <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d247.0143778636335!2d111.48797906817526!3d-7.870980106709049!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e79a0300ef1521d%3A0x80bc599492cab4d5!2sDinas%20Komunikasi%20Informatika%20dan%20Statistik%20Kabupaten%20Ponorogo!5e0!3m2!1sen!2sid!4v1750904774155!5m2!1sen!2sid"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe> --}}
        </div>
    </section>

    <!-- Footer Section -->
    <footer id="Kontak" class="py-12 text-gray-300 bg-gray-800 dark:bg-gray-950">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div>
                    <h3 class="mb-4 text-lg font-semibold text-white">Rakaca</h3>
                    <p class="mb-4">
                        Menyediakan solusi infrastruktur digital yang aman untuk organisasi.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 transition hover:text-white">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-400 transition hover:text-white">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="text-gray-400 transition hover:text-white">
                            <i class="fab fa-github"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="mb-4 text-lg font-semibold text-white">Kontak Kami</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="mt-1 mr-2 fas fa-envelope text-primary-400"></i>
                            <span>kominfo@ponorogo.go.id</span>
                        </li>
                        <li class="flex items-start">
                            <i class="mt-1 mr-2 fas fa-phone-alt text-primary-400"></i>
                            <span>0352-3592999</span>
                        </li>
                        <li class="flex items-start">
                            <i class="mt-1 mr-2 fas fa-map-marker-alt text-primary-400"></i>
                            <span>
                                Jl. Ir. H Juanda No.198, Tonatan, Kec. Ponorogo, Kabupaten Ponorogo, Jawa Timur 63418
                            </span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="mb-4 text-lg font-semibold text-white">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="#Layanan" class="transition hover:text-white">Layanan</a></li>
                        <li><a href="#Support" class="transition hover:text-white">Support</a></li>
                        {{-- <li><a href="#" class="transition hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="transition hover:text-white">Terms of Service</a></li> --}}
                    </ul>
                </div>
            </div>

            <div class="pt-8 mt-8 text-sm text-center text-gray-400 border-t border-gray-700">
                <p>&copy; {{ date('Y') }} Katalog Layanan TI. Dibuat dengan Bale di Bidang Aptika Dinas
                    Kominfo Kabupaten Ponorogo.</p>
            </div>
        </div>
    </footer>
</div>