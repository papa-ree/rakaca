<div
    class="group p-6 transition-all duration-300 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl hover:shadow-xl dark:border-gray-700 hover:-translate-y-1">
    <div class="flex items-center justify-between mb-4">
        <div class="p-3 bg-linear-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg">
            <x-lucide-server class="w-6 h-6 text-white" />
        </div>
        <span
            class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full dark:bg-blue-900/50 dark:text-blue-300">Aktif</span>
    </div>
    <div>
        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Layanan Aktif</p>
        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $this->activeServiceCount }}</p>
    </div>
    <div class="mt-4">
        <div class="w-full h-2 bg-gray-200 rounded-full dark:bg-gray-700 overflow-hidden">
            <div class="h-2 bg-linear-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500"
                style="width: {{ $this->percentage }}%"></div>
        </div>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
            {{ ($this->activeServiceCount > 0) ? $this->activeServiceCount . ' Layanan aktif' : 'Belum ada layanan aktif' }}
        </p>
    </div>
</div>