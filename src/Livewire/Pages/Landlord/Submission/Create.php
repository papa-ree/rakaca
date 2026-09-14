<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Title('Create Submission')]
class Create extends Component
{
    public $rakaca_form_id;

    public $code;

    public $status = 'pending';

    public $user_uuid;

    public $items = [];

    protected $rules = [
        'rakaca_form_id' => 'required|exists:rakaca_forms,id',
        'code' => 'required|unique:rakaca_submissions,code',
        'status' => 'required|in:pending,approved,rejected,review',
        'user_uuid' => 'required|uuid',
    ];

    public function mount()
    {
        if (! auth()->user()->can('submission.create')) {
            abort(403);
        }

        $this->code = strtoupper(Str::random(8));
        $this->user_uuid = auth()->user()->uuid ?? auth()->user()->id;
    }

    public function save()
    {
        $this->validate();

        RakacaSubmission::create([
            'rakaca_form_id' => $this->rakaca_form_id,
            'user_uuid' => $this->user_uuid,
            'code' => $this->code,
            'status' => $this->status,
            'items' => $this->items,
        ]);

        session()->flash('message', 'Submission created successfully.');

        return redirect()->route('rakaca.landlord.submission.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.create', [
            'forms' => Form::where('actived', true)->get(),
        ]);
    }
}
