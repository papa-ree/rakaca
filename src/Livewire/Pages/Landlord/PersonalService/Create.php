<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\PersonalService;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Paparee\Rakaca\Models\PersonHasService;
use Paparee\Rakaca\Models\RakacaService;

#[Layout('rakaca::layouts.app')]
#[Title('Assign Customer Service')]
class Create extends Component
{
    public string $user_uuid = '';
    public array $rakaca_service_ids = [];
    public bool $actived = true;

    public string $userSearch = '';

    protected function rules(): array
    {
        return [
            'user_uuid' => 'required|string',
            'rakaca_service_ids' => 'required|array|min:1',
            'rakaca_service_ids.*' => 'exists:rakaca_services,id',
            'actived' => 'boolean',
        ];
    }

    public function mount(): void
    {
        if (!auth()->user()->can('personal-service.create')) {
            abort(403);
        }
    }

    public function save(): void
    {
        $this->validate();

        foreach ($this->rakaca_service_ids as $serviceId) {
            // Prevent duplicate user+service combination
            PersonHasService::firstOrCreate(
                [
                    'user_uuid' => $this->user_uuid,
                    'rakaca_service_id' => $serviceId,
                ],
                [
                    'actived' => $this->actived,
                ]
            );
        }

        session()->flash('message', __('Customer service assigned successfully.'));

        $this->redirect(route('rakaca.landlord.personal-service.index'), navigate: true);
    }

    #[Computed]
    public function users()
    {
        return User::when($this->userSearch, function ($q) {
            $q->where('name', 'like', '%' . $this->userSearch . '%')
                ->orWhere('email', 'like', '%' . $this->userSearch . '%');
        })->orderBy('name')->limit(50)->get();
    }

    #[Computed]
    public function services()
    {
        return RakacaService::where('actived', true)->orderBy('name')->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.personal-service.create');
    }
}
