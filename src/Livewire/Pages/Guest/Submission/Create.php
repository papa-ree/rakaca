<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.guest')]
#[Title('New Submission')]
class Create extends Component
{
    public ?Form $selectedForm = null;

    public string $form_id = '';

    public array $items = [];

    public function mount()
    {
        $this->items = [];
    }

    public function updatedFormId($value)
    {
        if (empty($value)) {
            $this->selectedForm = null;
            $this->items = [];
            return;
        }

        $this->selectedForm = Form::with('service')->where('actived', true)->find($value);

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            $this->items = [];
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $this->items[$field['key']] = $field['type'] === 'checkbox' ? false : '';
            }
        }
    }

    protected function rules(): array
    {
        $rules = [
            'form_id' => 'required|uuid|exists:rakaca_forms,id',
        ];

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $rule = 'nullable|string|max:10000';
                if ($field['required'] ?? false) {
                    $rule = 'required|string|max:10000';
                }
                $rules["items.{$field['key']}"] = $rule;
            }
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [
            'form_id' => __('Form'),
        ];

        if ($this->selectedForm && $this->selectedForm->meta && isset($this->selectedForm->meta['fields'])) {
            foreach ($this->selectedForm->meta['fields'] as $field) {
                $attributes["items.{$field['key']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function save()
    {
        $this->validate();

        $submission = Submission::create([
            'user_uuid' => auth()->user()->uuid,
            'rakaca_form_id' => $this->form_id,
            'code' => strtoupper(uniqid('sub_')),
            'status' => 'pending',
            'items' => [
                'id' => Str::uuid()->toString(),
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString(),
                'data' => $this->items,
            ],
        ]);

        session()->flash('success', 'Pengajuan berhasil dikirim.');
        $this->redirectRoute('rakaca.guest-dashboard.index', navigate: true);
    }

    public function render()
    {
        $forms = Form::with('service')->where('actived', true)->get();

        return view('rakaca::livewire.pages.guest.submission.create', [
            'forms' => $forms,
        ]);
    }
}
