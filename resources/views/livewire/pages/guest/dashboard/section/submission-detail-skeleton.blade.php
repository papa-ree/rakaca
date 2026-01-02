{{-- Detail Modal Skeleton --}}
<div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: true }" x-show="show"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75"></div>

    {{-- Modal --}}
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl w-full max-w-2xl p-6 animate-pulse">
            {{-- Header Skeleton --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl"></div>
                    <div class="flex-1">
                        <div class="h-6 w-48 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                        <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
                <div class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>

            {{-- Content Skeleton --}}
            <div class="space-y-4 mb-6">
                <div class="h-4 w-full bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-4 w-5/6 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-4 w-4/6 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>

            {{-- Data Section Skeleton --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                    <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                    <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            </div>

            {{-- Footer Skeleton --}}
            <div class="flex justify-end">
                <div class="h-10 w-24 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
            </div>
        </div>
    </div>
</div>