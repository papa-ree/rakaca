<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    @for($i = 0; $i < 3; $i++)
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/[0.06] dark:bg-gray-900">
            <div class="flex items-start justify-between gap-3">
                <div class="h-11 w-11 animate-pulse rounded-xl bg-gray-100 dark:bg-gray-800"></div>
                <div class="h-5 w-14 animate-pulse rounded-full bg-gray-100 dark:bg-gray-800"></div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="h-8 w-20 animate-pulse rounded-lg bg-gray-100 dark:bg-gray-800"></div>
                <div class="h-3 w-32 animate-pulse rounded bg-gray-100 dark:bg-gray-800"></div>
            </div>
        </div>
    @endfor
</div>
