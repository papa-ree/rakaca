<div>
    <x-core::page-header :title="__('Create Submission')" :subtitle="__('Create a new service submission')">
        <x-slot name="actions">
            <a href="{{ route('rakaca.landlord.submission.index') }}" wire:navigate
                class="inline-flex items-center gap-x-2 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all shadow-sm">
                <x-lucide-arrow-left class="w-4 h-4" />
                {{ __('Back to List') }}
            </a>
        </x-slot>
    </x-core::page-header>

    <div class="max-w-4xl mx-auto">
        <div
            class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">
            <div
                class="p-6 border-b border-gray-100 dark:border-slate-800 bg-linear-to-r from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Submission Details') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Enter the submission information below') }}
                </p>
            </div>

            <form wire:submit="save" class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Service --}}
                    <div class="space-y-2">
                        <label
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Service') }}</label>
                        <select wire:model="rakaca_service_id"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            <option value="">{{ __('Select Service') }}</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                        @error('rakaca_service_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Code --}}
                    <div class="space-y-2">
                        <label
                            class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Submission Code') }}</label>
                        <input type="text" wire:model="code"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all font-mono"
                            readonly>
                        @error('code') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Status --}}
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Status') }}</label>
                        <select wire:model="status"
                            class="w-full px-4 py-2.5 bg-gray-50 dark:bg-slate-800 border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
                            <option value="pending">{{ __('Pending') }}</option>
                            <option value="review">{{ __('Review') }}</option>
                            <option value="approved">{{ __('Approved') }}</option>
                            <option value="rejected">{{ __('Rejected') }}</option>
                        </select>
                        @error('status') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- User UUID (Hidden or Readonly for now) --}}
                    <input type="hidden" wire:model="user_uuid">
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-slate-800 flex justify-end">
                    <button type="submit"
                        class="px-8 py-3 bg-linear-to-r from-indigo-500 to-purple-600 text-white font-bold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all shadow-lg hover:shadow-indigo-500/25 flex items-center gap-2">
                        <x-lucide-save class="w-5 h-5" />
                        {{ __('Save Submission') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>