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
        <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $this->pendingSubmissions }}</p>
    </div>
    @if ($this->pendingSubmissions < 1)
        <div class="mt-4">
            <p class="text-xs text-gray-500 dark:text-gray-400">Tidak ada pengajuan menunggu</p>
        </div>
    @endif
</div>