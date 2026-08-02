<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.guest')]
#[Title('Edit Submission')]
class Edit extends Component
{
    public ?Submission $submission = null;

    public array $items = [];

    public function mount(Submission $submission)
    {
        if ($submission->user_uuid !== auth()->user()->uuid) {
            abort(403);
        }

        if ($submission->status !== 'pending') {
            session()->flash('error', 'Hanya pengajuan dengan status menunggu yang bisa diedit.');
            $this->redirectRoute('rakaca.guest.submission.index', navigate: true);
            return;
        }

        $this->submission = $submission->load('form');
        $this->items = $submission->items['data'] ?? [];
    }

    protected function rules(): array
    {
        $rules = [];

        if ($this->submission && $this->submission->form && $this->submission->form->meta && isset($this->submission->form->meta['fields'])) {
            foreach ($this->submission->form->meta['fields'] as $field) {
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
        $attributes = [];

        if ($this->submission && $this->submission->form && $this->submission->form->meta && isset($this->submission->form->meta['fields'])) {
            foreach ($this->submission->form->meta['fields'] as $field) {
                $attributes["items.{$field['key']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function save()
    {
        $this->validate();

        $this->submission->update([
            'items' => [
                'id' => $this->submission->items['id'] ?? $this->submission->id,
                'created_at' => $this->submission->items['created_at'] ?? $this->submission->created_at->toISOString(),
                'updated_at' => now()->toISOString(),
                'data' => $this->items,
            ],
        ]);

        session()->flash('success', 'Pengajuan berhasil diperbarui.');
        $this->redirectRoute('rakaca.guest.submission.index', navigate: true);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.submission.edit');
    }
}
