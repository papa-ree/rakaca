<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\SelectBale;

use Bale\Cms\Models\BaleList;
use Bale\Cms\Models\BaleUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\Attributes\{Layout};

#[Layout('rakaca::layouts.app')]
#[Title('Rakaca | Select Bale')]
class Index extends Component
{
    public $bales = [];

    public function mount()
    {
        $userUuid = Auth::user()?->uuid;

        $baleIds = BaleUser::where('user_uuid', $userUuid)
            ->pluck('bale_id');

        $this->bales = BaleList::whereIn('id', $baleIds)->get();
    }

    public function selectBale(string $id)
    {
        session(['bale_active_uuid' => $id]);

        $selected_bale = BaleList::find($id);
        session(['bale_active_slug' => $selected_bale->slug]);

        return redirect()->route('bale.cms.overview');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.select-bale.index', [
            'bales' => $this->bales,
        ]);
    }
}