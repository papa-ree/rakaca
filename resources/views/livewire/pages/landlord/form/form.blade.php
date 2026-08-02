<div>
    <x-core::breadcrumb :items="[
        ['label' => __('Forms'), 'route' => 'rakaca.landlord.form.index'],
    ]" :active="$isEdit ? __('Edit Form') : __('Create Form')" />

    <div class="mt-6" x-data="{
            activeTab: 'builder',
            formName: $wire.entangle('name'),
            formSlug: $wire.entangle('slug'),
            actived: $wire.entangle('actived').live,
            fields: $wire.entangle('fields'),
            previewData: {},
            addField() {
                $wire.addField();
            },
            removeField(index) {
                $wire.removeField(index);
            },
            initPreview() {
                this.previewData = {};
                if (this.fields) {
                    this.fields.forEach(field => {
                        this.previewData[field.key] = field.type === 'checkbox' ? false : '';
                    });
                }
            },
            getPreviewJson() {
                return JSON.stringify(this.previewData, null, 2);
            }
        }">

        {{-- Tab Bar --}}
        <div class="mb-6 flex items-center gap-1 p-1 bg-gray-100 dark:bg-slate-800 rounded-xl w-fit">
            <button type="button" @click="activeTab = 'builder'; $event.preventDefault()"
                :class="activeTab === 'builder' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                class="inline-flex items-center gap-x-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                <x-lucide-hammer class="w-4 h-4" />
                {{ __('Builder') }}
            </button>
            <button type="button" @click="activeTab = 'preview'; initPreview(); $event.preventDefault()"
                :class="activeTab === 'preview' ? 'bg-white dark:bg-slate-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'"
                class="inline-flex items-center gap-x-2 px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                <x-lucide-eye class="w-4 h-4" />
                {{ __('Preview') }}
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">

            {{-- Main Content --}}
            <div class="flex-1 min-w-0 bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-xl overflow-hidden">

                {{-- ==================== BUILDER TAB ==================== --}}
                <div x-show="activeTab === 'builder'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <form wire:submit="save" class="p-8 space-y-6">

                        {{-- Section: Basic Info --}}
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                    <x-lucide-info class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ __('Basic Information') }}
                                    </h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                        {{ __('Form identity and service assignment') }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Name --}}
                                <div>
                                    <x-core::label for="name" :value="__('Form Name')" />
                                    <x-core::input id="name" type="text" class="block w-full mt-1" wire:model="name"
                                        x-model="formName" required placeholder="e.g. Formulir VPS" />
                                    <x-core::input-error for="name" class="mt-2" />
                                </div>

                                {{-- Slug --}}
                                <div>
                                    <x-core::label for="slug" :value="__('Slug')" />
                                    <x-core::input id="slug" type="text" class="block w-full mt-1" wire:model="slug"
                                        x-model="formSlug" x-slug="formName" required placeholder="formulir-vps" />
                                    <p class="mt-1 text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                        {{ $isEdit ? __('Unique identifier for the form') : __('Auto-generated from form name') }}
                                    </p>
                                    <x-core::input-error for="slug" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        {{-- Section: Service Assignment --}}
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                    <x-lucide-layers class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ __('Service Assignment') }}
                                    </h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                        {{ __('Select the IT service this form belongs to') }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <x-core::label for="rakaca_service_id" :value="__('Service')" />
                                <select id="rakaca_service_id" wire:model="rakaca_service_id"
                                    class="block w-full mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">-- {{ __('Select Service') }} --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                                <x-core::input-error for="rakaca_service_id" class="mt-2" />
                            </div>
                        </div>

                        {{-- Section: Status --}}
                        <div>
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                        <x-lucide-toggle-right class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <div>
                                        <label for="form-active-toggle" class="text-sm font-bold text-gray-900 dark:text-white cursor-pointer">
                                            {{ __('Enable Form') }}
                                        </label>
                                        <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                            {{ __('Show this form in the customer portal') }}
                                        </p>
                                    </div>
                                </div>
                                <label for="form-active-toggle" class="relative inline-block w-12 h-6 cursor-pointer">
                                    <input type="checkbox" id="form-active-toggle" x-model="actived" class="peer sr-only">
                                    <span class="absolute inset-0 bg-gray-300 dark:bg-slate-700 rounded-full transition-colors duration-300 peer-checked:bg-emerald-500"></span>
                                    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-sm transition-transform duration-300 peer-checked:translate-x-6"></span>
                                </label>
                            </div>
                            <x-core::input-error for="actived" class="mt-2" />
                        </div>

                        {{-- Section: Form Fields Builder --}}
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-amber-50 dark:bg-amber-900/20 rounded-lg">
                                    <x-lucide-list class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Form Fields') }}</h4>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                        {{ __('Define the input fields for this form') }}
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(field, index) in fields" :key="index">
                                    <div class="p-4 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-200 dark:border-slate-700">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider"
                                                x-text="'Field #' + (index + 1)"></span>
                                            <button type="button" @click="removeField(index)"
                                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {{-- Label --}}
                                            <div>
                                                <x-core::label :value="__('Label')" />
                                                <x-core::input type="text" class="block w-full mt-1"
                                                    x-model="fields[index].label"
                                                    x-on:input="fields[index].key = $event.target.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '')"
                                                    placeholder="e.g. Nama Lengkap" />
                                            </div>

                                            {{-- Key --}}
                                            <div>
                                                <x-core::label :value="__('Key')" />
                                                <x-core::input type="text" class="block w-full mt-1"
                                                    x-model="fields[index].key" placeholder="nama_lengkap" />
                                            </div>

                                            {{-- Type --}}
                                            <div>
                                                <x-core::label :value="__('Type')" />
                                                <div x-data="{
                                                    open: false,
                                                    types: [
                                                        { label: 'String', value: 'string' },
                                                        { label: 'Textarea', value: 'textarea' },
                                                        { label: 'Number', value: 'number' },
                                                        { label: 'Email', value: 'email' },
                                                        { label: 'Select', value: 'select' },
                                                        { label: 'Checkbox', value: 'checkbox' },
                                                        { label: 'Date', value: 'date' },
                                                        { label: 'File', value: 'file' }
                                                    ],
                                                    get selectedLabel() {
                                                        const found = this.types.find(t => t.value === fields[index].type);
                                                        return found ? found.label : '{{ __("Pilih...") }}';
                                                    }
                                                }" class="w-full relative" @keydown.escape="open = false" @click.outside="open = false">
                                                    <button type="button" @click="open = !open" :aria-expanded="open"
                                                        class="w-full flex items-center justify-between px-4 py-3 text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white transition-all duration-200 hover:border-purple-400 dark:hover:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent disabled:opacity-50 disabled:pointer-events-none"
                                                        :class="open ? 'border-purple-500 ring-2 ring-purple-500/30 dark:border-purple-500' : ''">
                                                        <span class="truncate" :class="fields[index].type ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'" x-text="selectedLabel"></span>
                                                        <svg class="w-4 h-4 shrink-0 text-gray-400 dark:text-gray-500 transition-transform duration-200 ml-2" :class="open ? 'rotate-180 text-purple-500' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                                                    </button>
                                                    <div x-show="open" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]" class="absolute z-50 mt-1.5 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl shadow-gray-200/50 dark:shadow-black/30 overflow-hidden" style="display: none;">
                                                        <div class="p-1.5 space-y-0.5 max-h-56 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent">
                                                            <template x-for="(type, typeIndex) in types" :key="typeIndex">
                                                                <button type="button" @click="fields[index].type = type.value; open = false;"
                                                                    class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg text-gray-700 dark:text-gray-300 transition-colors duration-150 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-400"
                                                                    :class="fields[index].type === type.value ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 font-semibold' : ''">
                                                                    <span x-text="type.label"></span>
                                                                    <svg x-show="fields[index].type === type.value" class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M20 6 9 17l-5-5"/></svg>
                                                                </button>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Required --}}
                                            <div class="flex items-center gap-2 mt-6">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <x-core::checkbox x-model="fields[index].required" />
                                                    <span class="text-sm text-gray-700 dark:text-gray-300 ml-2 select-none cursor-pointer">{{ __('Required') }}</span>
                                                </label>
                                            </div>

                                        {{-- Placeholder --}}
                                        <div class="mt-3">
                                            <x-core::label :value="__('Placeholder')" />
                                            <x-core::input type="text" class="block w-full mt-1"
                                                x-model="fields[index].placeholder"
                                                placeholder="e.g. Masukkan nama lengkap..." />
                                        </div>
                                    </div>
                                </template>

                                {{-- Add Field Button --}}
                                <button type="button" @click="addField()"
                                    class="inline-flex items-center gap-x-2 px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors">
                                    <x-lucide-plus class="w-4 h-4" />
                                    {{ __('Add Field') }}
                                </button>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-6 border-t border-gray-100 dark:border-slate-800">
                            <x-core::secondary-button link href="{{ route('rakaca.landlord.form.index') }}"
                                label="{{ __('Cancel') }}" />

                            <x-core::button type="submit" spinner="save"
                                label="{{ $isEdit ? __('Update Form') : __('Create Form') }}">
                                <x-slot name="icon">
                                    @if ($isEdit)
                                        <x-lucide-check class="w-4 h-4" />
                                    @else
                                        <x-lucide-plus class="w-4 h-4" />
                                    @endif
                                </x-slot>
                            </x-core::button>
                        </div>
                    </form>
                </div>

                {{-- ==================== PREVIEW TAB ==================== --}}
                <div x-show="activeTab === 'preview'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <div class="p-8">

                        {{-- Preview Header --}}
                        <div class="flex items-center gap-3 mb-6">
                            <div class="p-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg">
                                <x-lucide-eye class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ __('Form Preview') }}
                                </h4>
                                <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase tracking-wider">
                                    {{ __('How users will see and fill this form') }}
                                </p>
                            </div>
                        </div>

                        {{-- Preview Form Card --}}
                        <div class="bg-linear-to-br from-indigo-50/50 to-purple-50/50 dark:from-indigo-900/10 dark:to-purple-900/10 rounded-xl border border-indigo-100 dark:border-indigo-800/30 p-6">
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="formName || '{{ __('Untitled Form') }}'"></h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400" x-text="'/' + (formSlug || 'untitled')"></p>
                            </div>

                            {{-- No Fields Warning --}}
                            <div x-show="!fields || fields.length === 0" class="text-center py-8">
                                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-gray-200 dark:bg-slate-700 rounded-full mb-3">
                                    <x-lucide-inbox class="w-6 h-6 text-gray-400 dark:text-gray-500" />
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No fields defined yet. Add fields in the Builder tab.') }}</p>
                            </div>

                            {{-- Dynamic Preview Fields --}}
                            <div x-show="fields && fields.length > 0" class="space-y-5">
                                <template x-for="(field, index) in fields" :key="'preview-' + index">
                                    <div>
                                        {{-- Label --}}
                                        <label class="block capitalize text-sm font-medium mb-2 dark:text-white">
                                            <span x-text="field.label || 'Field'"></span>
                                            <span x-show="field.required" class="text-red-500 ml-0.5">*</span>
                                        </label>

                                        {{-- String --}}
                                        <div x-show="field.type === 'string'">
                                            <input type="text"
                                                x-model="previewData[field.key]"
                                                :placeholder="field.placeholder || ''"
                                                :required="field.required"
                                                class="block w-full py-3 px-4 text-gray-900 placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 form-input rounded-xl dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                                        </div>

                                        {{-- Textarea --}}
                                        <div x-show="field.type === 'textarea'">
                                            <textarea
                                                x-model="previewData[field.key]"
                                                :placeholder="field.placeholder || ''"
                                                rows="3"
                                                class="block w-full py-3 px-4 text-gray-900 placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 form-input rounded-xl dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                                        </div>

                                        {{-- Number --}}
                                        <div x-show="field.type === 'number'">
                                            <input type="number"
                                                x-model="previewData[field.key]"
                                                :placeholder="field.placeholder || ''"
                                                :required="field.required"
                                                class="block w-full py-3 px-4 text-gray-900 placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 form-input rounded-xl dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                                        </div>

                                        {{-- Email --}}
                                        <div x-show="field.type === 'email'">
                                            <input type="email"
                                                x-model="previewData[field.key]"
                                                :placeholder="field.placeholder || ''"
                                                :required="field.required"
                                                class="block w-full py-3 px-4 text-gray-900 placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 form-input rounded-xl dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                                        </div>

                                        {{-- Date --}}
                                        <div x-show="field.type === 'date'">
                                            <input type="date"
                                                x-model="previewData[field.key]"
                                                :required="field.required"
                                                class="block w-full py-3 px-4 text-gray-900 placeholder-gray-500 transition-all duration-200 bg-white border border-gray-300 form-input rounded-xl dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                                        </div>

                                        {{-- Select --}}
                                        <div x-show="field.type === 'select'">
                                            <select x-model="previewData[field.key]"
                                                :required="field.required"
                                                class="block w-full py-3 px-4 text-gray-900 transition-all duration-200 bg-white border border-gray-300 form-input rounded-xl dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                                <option value="">{{ __('-- Select --') }}</option>
                                            </select>
                                        </div>

                                        {{-- Checkbox --}}
                                        <div x-show="field.type === 'checkbox'">
                                            <div class="flex items-center gap-2">
                                                <input type="checkbox"
                                                    x-model="previewData[field.key]"
                                                    class="peer sr-only" />
                                                <span class="shrink-0 flex items-center justify-center size-4.5 rounded-md border-2 transition-all duration-200 border-gray-300 dark:border-gray-600 peer-checked:border-purple-500 peer-checked:bg-purple-500 dark:peer-checked:border-purple-500 dark:peer-checked:bg-purple-500 text-transparent peer-checked:text-white">
                                                    <x-lucide-check class="size-2.5" />
                                                </span>
                                                <label class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer"
                                                    x-text="field.placeholder || field.label"></label>
                                            </div>
                                        </div>

                                        {{-- File --}}
                                        <div x-show="field.type === 'file'">
                                            <input type="file"
                                                class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-900/20 dark:file:text-indigo-400 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/30" />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- JSON Output --}}
                        <div class="mt-6">
                            <div class="bg-gray-900 dark:bg-slate-800 rounded-xl border border-gray-700 dark:border-slate-700 overflow-hidden">
                                {{-- Code Block Header --}}
                                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-800 dark:bg-slate-700/50 border-b border-gray-700 dark:border-slate-700">
                                    <div class="flex items-center gap-2">
                                        <div class="flex gap-1.5">
                                            <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                                            <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                                        </div>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wider font-medium ml-2">
                                            {{ __('Test Data Output') }}
                                        </span>
                                    </div>
                                    <button type="button" x-ref="copyBtn"
                                        @click="navigator.clipboard.writeText(getPreviewJson()).then(() => { let orig = $refs.copyBtn.textContent.trim(); $refs.copyBtn.textContent = '{{ __("Copied!") }}'; setTimeout(() => $refs.copyBtn.textContent = orig, 2000) })"
                                        class="inline-flex items-center gap-x-1.5 px-2.5 py-1 text-[11px] font-medium text-gray-300 dark:text-gray-400 bg-gray-700/50 dark:bg-gray-600/30 rounded-md hover:bg-gray-700 dark:hover:bg-gray-600/50 hover:text-white dark:hover:text-gray-200 transition-colors">
                                        <x-lucide-copy class="w-3 h-3" />
                                        {{ __('Copy') }}
                                    </button>
                                </div>
                                {{-- Code Block Body --}}
                                <pre class="p-4 text-sm text-green-400 font-mono overflow-x-auto leading-relaxed"
                                    x-text="getPreviewJson()"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="w-full lg:w-80 shrink-0">
                <div class="bg-linear-to-b from-indigo-50/80 to-purple-50/80 dark:from-indigo-900/15 dark:to-purple-900/15 rounded-2xl border border-indigo-100 dark:border-indigo-800/30 shadow-xl overflow-hidden lg:sticky lg:top-6">

                    {{-- Sidebar Header --}}
                    <div class="p-6 border-b border-indigo-100/60 dark:border-indigo-800/20">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-linear-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg shadow-indigo-500/25">
                                @if ($isEdit)
                                    <x-lucide-settings class="w-5 h-5 text-white" />
                                @else
                                    <x-lucide-plus-circle class="w-5 h-5 text-white" />
                                @endif
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $isEdit ? __('Edit Form') : __('New Form') }}
                                </h3>
                                <p class="text-[10px] text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-medium">
                                    {{ $isEdit ? __('Updating existing') : __('Creating new') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar Body --}}
                    <div class="p-6 space-y-5">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-1">
                                {{ $isEdit ? __('About Edit') : __('About Create') }}
                            </h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                {{ $isEdit
    ? __('Update the form fields and settings. Use Preview tab to test how users will see the form.')
    : __('Define the form structure for a specific IT service. Use Preview tab to test the form before saving.') }}
                            </p>
                        </div>

                        {{-- Quick Tips --}}
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">
                                {{ __('Quick Tips') }}
                            </h4>
                            <ul class="space-y-2.5">
                                <li class="flex items-start gap-2.5">
                                    <div class="shrink-0 mt-0.5 w-1.5 h-1.5 rounded-full bg-indigo-500"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Field keys are auto-generated from labels') }}
                                    </span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="shrink-0 mt-0.5 w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Slug is auto-generated from the form name') }}
                                    </span>
                                </li>
                                <li class="flex items-start gap-2.5">
                                    <div class="shrink-0 mt-0.5 w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ __('Use Preview tab to test form before saving') }}
                                    </span>
                                </li>
                            </ul>
                        </div>

                        {{-- Field Stats --}}
                        <div x-show="fields && fields.length > 0">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-3">
                                {{ __('Field Stats') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700">
                                    <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400" x-text="fields ? fields.length : 0"></p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase">{{ __('Total Fields') }}</p>
                                </div>
                                <div class="p-3 bg-white dark:bg-slate-800 rounded-lg border border-slate-100 dark:border-slate-700">
                                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400" x-text="fields ? fields.filter(f => f.required).length : 0"></p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-500 uppercase">{{ __('Required') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar Footer --}}
                    <div class="px-6 py-4 border-t border-indigo-100/60 dark:border-indigo-800/20">
                        <div class="flex items-center gap-2">
                            <div class="shrink-0 w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                                <x-lucide-shield-check class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                {{ __('Requires :permission permission', ['permission' => $isEdit ? 'form.update' : 'form.create']) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>