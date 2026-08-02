<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Section;

use App\Models\User;
use Bale\Cms\Services\TenantManager;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Paparee\Rakaca\Models\BaleUser;

#[Layout('rakaca::layouts.app')]
class Table extends Component
{
    use WithPagination;

    public $query = '';

    public $perPage = 20;

    public function mount()
    {
        if (! auth()->user()->can('bale-user.read')) {
            abort(403);
        }
    }

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sort($field)
    {
        // No-op: table is sorted by name always
    }

    #[On('deleteItem')]
    public function deleteBaleUser($id)
    {
        if (! auth()->user()->can('bale-user.delete')) {
            abort(403);
        }

        $baleUser = BaleUser::findOrFail($id);
        $baleId = $baleUser->bale_id;
        $userUuid = $baleUser->user_uuid;

        if ($baleUser->user?->hasRole('root') && ! auth()->user()->hasRole('root')) {
            abort(403, __('Only users with root role can delete this assignment.'));
        }

        DB::transaction(function () use ($baleUser, $baleId, $userUuid) {
            $this->deleteUserFromTenant($baleId, $userUuid);
            $baleUser->delete();
        });

        $this->dispatch('toast', message: __('Data deleted successfully!'), type: 'success');
    }

    protected function deleteUserFromTenant($baleId, $userUuid)
    {
        try {
            TenantManager::initializeFromBaleUuid($baleId);
            $connection = TenantManager::getActiveConnection();

            DB::connection($connection)->table('users')->where('uuid', $userUuid)->delete();
        } catch (\Exception $e) {
            info('Failed to delete user from tenant: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = User::query()
            ->with(['baleUsers.bale'])
            ->whereHas('baleUsers');

        if (filled($this->query)) {
            $search = $this->query;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('baleUsers', function ($q2) use ($search) {
                        $q2->where('role', 'like', "%{$search}%")
                            ->orWhereHas('bale', function ($q3) use ($search) {
                                $q3->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $users = $query->orderBy('name')->paginate($this->perPage);

        return view('rakaca::livewire.pages.landlord.bale-user.section.table', [
            'users' => $users,
        ]);
    }
}
