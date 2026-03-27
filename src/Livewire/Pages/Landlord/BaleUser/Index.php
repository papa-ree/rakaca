<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\BaleUser;
use Bale\Cms\Services\TenantManager;
use Illuminate\Support\Facades\DB;

#[Layout('rakaca::layouts.app')]
#[Title('Bale User Management')]
class Index extends Component
{
    use WithPagination;

    public $query = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        if (!auth()->user()->can('bale-user.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-user.index');
    }

    #[Computed]
    public function baleUsers()
    {
        return BaleUser::query()
            ->with(['bale', 'user'])
            ->when($this->query, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->query . '%')
                        ->orWhere('email', 'like', '%' . $this->query . '%');
                })->orWhereHas('bale', function ($q) {
                    $q->where('name', 'like', '%' . $this->query . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('deleteItem')]
    public function deleteBaleUser($id)
    {
        if (!auth()->user()->can('bale-user.delete')) {
            abort(403);
        }

        $baleUser = BaleUser::findOrFail($id);
        $baleId = $baleUser->bale_id;
        $userUuid = $baleUser->user_uuid;

        // Protection for root role: only another root can delete a root user assignment
        if ($baleUser->user?->hasRole('root') && !auth()->user()->hasRole('root')) {
            abort(403, __('Only users with root role can delete this assignment.'));
        }

        DB::transaction(function () use ($baleUser, $baleId, $userUuid) {
            // Remove from tenant first
            $this->deleteUserFromTenant($baleId, $userUuid);

            // Remove from landlord
            $baleUser->delete();
        });

        $this->dispatch('toast', message: __('Data deleted successfully!'), type: 'success');
        $this->dispatch('paginated');
    }

    protected function deleteUserFromTenant($baleId, $userUuid)
    {
        try {
            TenantManager::initializeFromBaleUuid($baleId);
            $connection = TenantManager::getActiveConnection();

            DB::connection($connection)->table('users')->where('uuid', $userUuid)->delete();
        } catch (\Exception $e) {
            info("Failed to delete user from tenant: " . $e->getMessage());
        }
    }
}
