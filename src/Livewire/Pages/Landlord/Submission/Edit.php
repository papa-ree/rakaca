<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Service;
use Paparee\Rakaca\Models\Submission;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Submission')]
class Edit extends Component
{
    public Submission $submission;

    public $rakaca_service_id;

    public $code;

    public $status;

    public $data;

    protected function rules()
    {
        return [
            'rakaca_service_id' => 'required|exists:rakaca_services,id',
            'code' => 'required|unique:submissions,code,'.$this->submission->id,
            'status' => 'required|in:pending,approved,rejected,review',
        ];
    }

    public function mount(Submission $submission)
    {
        if (! auth()->user()->can('submission.update')) {
            abort(403);
        }

        $this->submission = $submission;
        $this->rakaca_service_id = $submission->rakaca_service_id;
        $this->code = $submission->code;
        $this->status = $submission->status;
        $this->data = $submission->data;
    }

    public function save()
    {
        $this->validate();

        $this->submission->update([
            'rakaca_service_id' => $this->rakaca_service_id,
            'status' => $this->status,
            'data' => $this->data,
        ]);

        session()->flash('message', 'Submission updated successfully.');

        return redirect()->route('rakaca.landlord.submission.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.edit', [
            'services' => Service::where('actived', true)->get(),
        ]);
    }
}
