<?php

namespace Paparee\Rakaca\Livewire\Pages\Guest\Dashboard;

use Bale\Core\Support\Sanitize;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Title('Guest Dashboard')]
class Index extends Component
{
    #[Computed()]
    public function totalServices()
    {
        return RakacaService::where('actived', true)->count();
    }

    #[Computed]
    public function pendingSubmissionsOwn(): int
    {
        return RakacaSubmission::where('user_uuid', auth()->user()->uuid)->where('status', 'pending')->count();
    }

    #[Computed]
    public function totalSubmissionsOwn(): int
    {
        return RakacaSubmission::where('user_uuid', auth()->user()->uuid)->count();
    }

    #[Computed]
    public function lastSubmissions()
    {
        return RakacaSubmission::where('user_uuid', auth()->user()->uuid)
            ->with(['form.service', 'uploads'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.guest.dashboard.index');
    }
}
