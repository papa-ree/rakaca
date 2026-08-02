<div>
    <div id="services"
        class="p-6 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl dark:border-gray-700"
        data-aos="fade-up" data-aos-delay="300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Jenis Layanan Tersedia
            </h3>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @forelse($this->services as $service)
                <a href="https://wa.me/6285126061182" target="_blank"
                    class="group relative overflow-hidden p-5 transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 dark:border-gray-700 rounded-2xl hover:shadow-xl hover:-translate-y-1">
                    {{-- Gradient Accent --}}
                    <div
                        class="absolute top-0 inset-x-0 h-1 bg-linear-to-r from-purple-500 via-pink-500 to-blue-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    </div>

                    <div class="flex items-center gap-4">
                        <div
                            class="shrink-0 flex items-center justify-center w-11 h-11 bg-linear-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg transition-transform duration-200 group-hover:scale-110">
                            <x-dynamic-component :component="'lucide-' . $service->icon"
                                class="w-5 h-5 text-white" />
                        </div>
                        <div class="min-w-0">
                            <h4
                                class="text-sm font-bold text-gray-900 transition-colors dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 truncate">
                                {{ $service->name }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-0.5">
                                {{ $service->description }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-12">
                    <div
                        class="flex items-center justify-center w-20 h-20 mx-auto mb-4 rounded-full bg-gray-100 dark:bg-gray-700">
                        <x-lucide-inbox class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Belum Ada Layanan</h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Layanan sedang dalam persiapan. Silakan hubungi kami untuk informasi lebih lanjut.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</div>
