<div class="space-y-4 sm:space-y-5">
    @if(empty($schema))
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Tidak ada skema hasil layanan. Isi hasil di bawah ini.') }}</p>
        <div class="space-y-1.5">
            <x-core::label for="resolution_manual" :value="__('Hasil Layanan')" class="text-sm font-medium" />
            <x-core::textarea id="resolution_manual" rows="4"
                wire:model="resolution.manual_result" :placeholder="__('Isi hasil layanan...')" />
        </div>
    @else
        @foreach ($schema as $field)
            @php $fieldKey = $field['key'] ?? ''; @endphp
            @if(!$fieldKey) @continue @endif
            <div class="space-y-1.5">
                <x-core::label for="res_{{ $fieldKey }}" :value="__($field['label'] ?? $fieldKey)" class="text-sm font-medium" />

                @if (($field['type'] ?? 'string') === 'textarea')
                    <x-core::textarea id="res_{{ $fieldKey }}" rows="3" wire:model="resolution.{{ $fieldKey }}" />

                @elseif (($field['type'] ?? 'string') === 'number')
                    <x-core::input id="res_{{ $fieldKey }}" type="number" inputmode="numeric" wire:model="resolution.{{ $fieldKey }}" />

                @elseif (($field['type'] ?? 'string') === 'email')
                    <x-core::input id="res_{{ $fieldKey }}" type="email" inputmode="email" wire:model="resolution.{{ $fieldKey }}" />

                @elseif (($field['type'] ?? 'string') === 'date')
                    <x-core::input id="res_{{ $fieldKey }}" type="date" wire:model="resolution.{{ $fieldKey }}" />

                @elseif (($field['type'] ?? 'string') === 'select')
                    <select id="res_{{ $fieldKey }}" wire:model="resolution.{{ $fieldKey }}" class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent py-3 px-4 text-sm transition-all duration-200">
                        <option value="">-- {{ __('Pilih') }} --</option>
                        @if(!empty($field['options']))
                            @foreach($field['options'] as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        @endif
                    </select>

                @elseif (($field['type'] ?? 'string') === 'checkbox')
                    <label class="flex items-center gap-3 py-2 px-1 cursor-pointer">
                        <x-core::checkbox id="res_{{ $fieldKey }}" wire:model="resolution.{{ $fieldKey }}" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __($field['placeholder'] ?? $field['label'] ?? $fieldKey) }}</span>
                    </label>

                @else
                    <x-core::input id="res_{{ $fieldKey }}" type="text" wire:model="resolution.{{ $fieldKey }}" :placeholder="__($field['placeholder'] ?? '')" />
                @endif
            </div>
        @endforeach
    @endif
</div>