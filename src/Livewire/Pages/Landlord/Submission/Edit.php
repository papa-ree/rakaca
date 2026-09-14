<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Submission')]
class Edit extends Component
{
    public RakacaSubmission $submission;

    public $rakaca_form_id;

    public $code;

    public $status;

    public $items;

    protected function rules()
    {
        return [
            'rakaca_form_id' => 'required|exists:rakaca_forms,id',
            'code' => 'required|unique:rakaca_submissions,code,'.$this->submission->id,
            'status' => 'required|in:pending,approved,rejected,review',
        ];
    }

    public function mount(RakacaSubmission $submission)
    {
        if (! auth()->user()->can('submission.update')) {
            abort(403);
        }

        $this->submission = $submission;
        $this->rakaca_form_id = $submission->rakaca_form_id;
        $this->code = $submission->code;
        $this->status = $submission->status;
        $this->items = $submission->items;
    }

    public function save()
    {
        $this->validate();

        $this->submission->update([
            'rakaca_form_id' => $this->rakaca_form_id,
            'status' => $this->status,
            'items' => $this->items,
        ]);

        session()->flash('message', 'Submission updated successfully.');

        return redirect()->route('rakaca.landlord.submission.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.edit', [
            'forms' => Form::where('actived', true)->get(),
        ]);
    }
}
