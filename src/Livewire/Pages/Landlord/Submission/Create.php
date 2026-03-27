<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title};
use Paparee\Rakaca\Models\Submission;
use Paparee\Rakaca\Models\Service;
use Illuminate\Support\Str;

#[Layout('rakaca::layouts.app')]
#[Title('Create Submission')]
class Create extends Component
{
    public $rakaca_service_id;
    public $code;
    public $status = 'pending';
    public $user_uuid;
    public $data = [];

    protected $rules = [
        'rakaca_service_id' => 'required|exists:rakaca_services,id',
        'code' => 'required|unique:submissions,code',
        'status' => 'required|in:pending,approved,rejected,review',
        'user_uuid' => 'required|uuid',
    ];

    public function mount()
    {
        if (!auth()->user()->can('submission.create')) {
            abort(403);
        }

        $this->code = strtoupper(Str::random(8));
        $this->user_uuid = auth()->user()->uuid ?? auth()->user()->id; // Fallback to id if uuid not present
    }

    public function save()
    {
        $this->validate();

        Submission::create([
            'rakaca_service_id' => $this->rakaca_service_id,
            'user_uuid' => $this->user_uuid,
            'code' => $this->code,
            'status' => $this->status,
            'data' => $this->data,
        ]);

        session()->flash('message', 'Submission created successfully.');

        return redirect()->route('rakaca.landlord.submission.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.create', [
            'services' => Service::where('actived', true)->get()
        ]);
    }
}
