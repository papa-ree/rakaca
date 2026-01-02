<div class="mb-8">
    <div class="p-6 bg-white border border-gray-100 shadow-md dark:bg-gray-800 rounded-2xl dark:border-gray-700">
        <div class="space-y-3">
            @for($i = 0; $i < 3; $i++)
                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-xl animate-pulse">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="w-9 h-9 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                            <div class="flex-1">
                                <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                                <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                            </div>
                        </div>
                        <div class="h-6 w-24 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>