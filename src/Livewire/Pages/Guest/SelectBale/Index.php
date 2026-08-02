<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\SelectBale;

use Bale\Cms\Models\BaleList;
use Bale\Cms\Models\BaleUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('rakaca::layouts.app')]
#[Title('Rakaca | Select Bale')]
class Index extends Component
{
    public $userUuid;

    public function mount()
    {
        $this->userUuid = Auth::user()?->uuid;
    }

    public function selectBale(string $id)
    {
        session(['bale_active_uuid' => $id]);

        $selected_bale = BaleList::find($id);
        session(['bale_active_slug' => $selected_bale->slug]);

        $user_role = BaleUser::whereUserUuid($this->userUuid)->whereBaleId($id)->first();
        session(['bale_active_user_role' => $user_role->role]);
        session(['bale_active_user_uuid' => $this->userUuid]);

        return redirect()->route('bale.cms.overview');
    }

    public function render()
    {
        $baleIds = BaleUser::where('user_uuid', $this->userUuid)
            ->pluck('bale_id');

        $bales = BaleList::whereIn('id', $baleIds)->get();

        return view('rakaca::livewire.pages.guest.select-bale.index', [
            'bales' => $bales,
        ]);
    }
}
