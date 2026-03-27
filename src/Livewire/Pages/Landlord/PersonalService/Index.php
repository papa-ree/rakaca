<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\PersonalService;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout, Title, Computed, On};
use Paparee\Rakaca\Models\PersonHasService;

#[Layout('rakaca::layouts.app')]
#[Title('Personal Service Management')]
class Index extends Component
{
    use WithPagination;

    public string $query = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function mount(): void
    {
        if (!auth()->user()->can('personal-service.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.personal-service.index');
    }

    #[Computed]
    public function customers()
    {
        // Get distinct user_uuids from person_has_services
        $query = PersonHasService::with(['user', 'service'])
            ->when($this->query, function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('name', 'like', '%' . $this->query . '%')
                        ->orWhere('email', 'like', '%' . $this->query . '%');
                })->orWhereHas('service', function ($sq) {
                    $sq->where('name', 'like', '%' . $this->query . '%');
                });
            });

        // Get unique user_uuids with pagination
        $userUuids = $query->select('user_uuid')
            ->distinct()
            ->orderBy('user_uuid')
            ->pluck('user_uuid');

        // Manual pagination on user_uuid level
        $perPage = 10;
        $page = $this->getPage();
        $total = $userUuids->count();
        $pagedUuids = $userUuids->slice(($page - 1) * $perPage, $perPage)->values();

        // For each user, load their services
        $customers = $pagedUuids->map(function ($uuid) {
            $user = User::where('uuid', $uuid)->first();
            $services = PersonHasService::with('service')
                ->where('user_uuid', $uuid)
                ->get();
            return [
                'user' => $user,
                'uuid' => $uuid,
                'services' => $services,
                'active_count' => $services->where('actived', true)->count(),
            ];
        });

        // Build paginator
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $customers,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginator;
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('deleteCustomer')]
    public function deleteCustomer(string $uuid): void
    {
        if (!auth()->user()->can('personal-service.delete')) {
            abort(403);
        }

        PersonHasService::where('user_uuid', $uuid)->delete();

        session()->flash('message', __('Customer deleted successfully.'));
        $this->dispatch('paginated');
    }

    #[On('deleteService')]
    public function deleteService(string $id): void
    {
        if (!auth()->user()->can('personal-service.delete')) {
            abort(403);
        }

        PersonHasService::findOrFail($id)->delete();

        session()->flash('message', __('Service removed from customer.'));
        $this->dispatch('paginated');
    }
}
