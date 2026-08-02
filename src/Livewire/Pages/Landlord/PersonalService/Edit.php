<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\PersonalService;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\PersonHasService;
use Paparee\Rakaca\Models\RakacaService;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Customer Service')]
class Edit extends Component
{
    public PersonHasService $personalService;

    public bool $actived;

    public string $rakaca_service_id;

    protected function rules(): array
    {
        return [
            'rakaca_service_id' => 'required|exists:rakaca_services,id|unique:person_has_services,rakaca_service_id,'
                .$this->personalService->id
                .',id,user_uuid,'.$this->personalService->user_uuid,
            'actived' => 'boolean',
        ];
    }

    public function mount(PersonHasService $personalService): void
    {
        if (! auth()->user()->can('personal-service.update')) {
            abort(403);
        }

        $this->personalService = $personalService;
        $this->actived = (bool) $personalService->actived;
        $this->rakaca_service_id = $personalService->rakaca_service_id;
    }

    public function save(): void
    {
        $this->validate();

        $this->personalService->update([
            'rakaca_service_id' => $this->rakaca_service_id,
            'actived' => $this->actived,
        ]);

        session()->flash('message', __('Customer service updated successfully.'));

        $this->redirect(route('rakaca.landlord.personal-service.index'), navigate: true);
    }

    #[Computed]
    public function services()
    {
        return RakacaService::orderBy('name')->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.personal-service.edit');
    }
}
