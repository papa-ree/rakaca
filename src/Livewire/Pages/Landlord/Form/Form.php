<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Form;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Form as FormModel;
use Paparee\Rakaca\Models\Service;

#[Layout('rakaca::layouts.app')]
#[Title('Form Builder')]
class Form extends Component
{
    public ?FormModel $formModel = null;

    public bool $isEdit = false;

    public string $rakaca_service_id = '';

    public string $name = '';

    public string $slug = '';

    public bool $actived = true;

    public array $meta = [
        'fields' => []
    ];

    public array $fields = [];

    public function mount(?FormModel $form = null): void
    {
        if ($form && $form->exists) {
            if (!auth()->user()->can('form.update')) {
                abort(403);
            }

            $this->isEdit = true;
            $this->formModel = $form;
            $this->rakaca_service_id = $form->rakaca_service_id;
            $this->name = $form->name;
            $this->slug = $form->slug;
            $this->actived = $form->actived;
            $this->meta = $form->meta ?? ['fields' => []];
            $this->fields = $this->meta['fields'] ?? [];
        } else {
            if (!auth()->user()->can('form.create')) {
                abort(403);
            }
            // Add a default field
            $this->addField();
        }
    }

    public function updatedName(string $value): void
    {
        $this->name = Sanitize::text($value);
        $this->slug = Str::slug($this->name);
    }

    public function updatedSlug(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function addField(): void
    {
        $this->fields[] = [
            'key' => '',
            'label' => '',
            'type' => 'string',
            'required' => false,
            'placeholder' => '',
            'order' => count($this->fields) + 1,
        ];
    }

    public function removeField(int $index): void
    {
        if (isset($this->fields[$index])) {
            unset($this->fields[$index]);
            $this->fields = array_values($this->fields);
            foreach ($this->fields as $i => $field) {
                $this->fields[$i]['order'] = $i + 1;
            }
        }
    }

    public function updateFieldKeyFromLabel(int $index, string $label): void
    {
        if (isset($this->fields[$index])) {
            $this->fields[$index]['label'] = Sanitize::text($label);
            $this->fields[$index]['key'] = Str::snake(Str::lower($label));
        }
    }

    protected function rules(): array
    {
        $uniqueRule = $this->isEdit
            ? 'required|unique:rakaca_forms,slug,' . $this->formModel->id
            : 'required|unique:rakaca_forms,slug';

        return [
            'rakaca_service_id' => 'required|uuid|exists:rakaca_services,id',
            'name' => 'required|min:3|max:255',
            'slug' => $uniqueRule,
            'actived' => 'boolean',
            'fields' => 'nullable|array',
            'fields.*.key' => 'required|string|distinct|max:255',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:string,textarea,number,email,select,checkbox,date,file',
            'fields.*.required' => 'boolean',
            'fields.*.placeholder' => 'nullable|string|max:255',
            'fields.*.order' => 'required|integer',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'rakaca_service_id' => __('Service'),
            'fields.*.key' => __('Field Key'),
            'fields.*.label' => __('Field Label'),
            'fields.*.type' => __('Field Type'),
        ];
    }

    public function save(): void
    {
        $this->name = Sanitize::text($this->name);
        $this->slug = Str::slug($this->slug);

        // Sanitize fields
        foreach ($this->fields as $i => $field) {
            $this->fields[$i]['key'] = Str::snake(Str::lower(Sanitize::text($field['key'])));
            $this->fields[$i]['label'] = Sanitize::text($field['label']);
            $this->fields[$i]['placeholder'] = Sanitize::text($field['placeholder'] ?? '');
        }

        // Sync fields into meta
        $this->meta['fields'] = $this->fields;

        $this->validate();

        $data = [
            'rakaca_service_id' => $this->rakaca_service_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'meta' => $this->meta,
            'actived' => $this->actived,
        ];

        if ($this->isEdit) {
            $this->formModel->update($data);
            session()->flash('success', 'Form updated successfully.');
        } else {
            FormModel::create($data);
            session()->flash('success', 'New form created successfully.');
        }

        $this->redirectRoute('rakaca.landlord.form.index', navigate: true);
    }

    public function render()
    {
        $services = Service::where('actived', true)->get();
        return view('rakaca::livewire.pages.landlord.form.form', [
            'services' => $services,
        ]);
    }
}