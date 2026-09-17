<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Dashboard;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Title('Rakaca | Dashboard')]
class Index extends Component
{
    #[Computed]
    public function totalServices(): int
    {
        return RakacaService::count();
    }

    #[Computed]
    public function activeServices(): int
    {
        return RakacaService::where('actived', true)->count();
    }

    #[Computed]
    public function pendingSubmissions(): int
    {
        return RakacaSubmission::whereIn('status', [
            SubmissionStatus::SiapDireview->value,
            SubmissionStatus::Diproses->value,
        ])->count();
    }

    #[Computed]
    public function totalUsers(): int
    {
        return User::count();
    }

    #[Computed]
    public function recentSubmissions()
    {
        return RakacaSubmission::with(['form.service', 'user', 'uploads'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.dashboard.index');
    }
}
