<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Paparee\Rakaca\Models\BaleList;
use Paparee\Rakaca\Models\BaleUser;
use Paparee\Rakaca\Models\PersonHasService;
use App\Models\User;
use Bale\Cms\Services\TenantManager;
use Illuminate\Support\Facades\DB;

#[Layout('rakaca::layouts.app')]
class Form extends Component
{
    public $baleUserId;
    public $bale_id;
    public $user_uuid;
    public $role = 'user';
    public $isEdit = false;

    protected function rules()
    {
        $validRolesString = implode(',', config('cms.tenant_roles', ['root', 'admin', 'user']));

        return [
            'bale_id' => 'required|exists:bale_lists,id',
            'role' => "required|in:{$validRolesString}",
            'user_uuid' => [
                'required',
                'exists:users,uuid',
                function ($attribute, $value, $fail) {
                    // Check unique combination
                    $query = BaleUser::where('bale_id', $this->bale_id)
                        ->where('user_uuid', $value);

                    if ($this->isEdit) {
                        $query->where('id', '!=', $this->baleUserId);
                    }

                    if ($query->exists()) {
                        $fail(__('This user is already assigned to the selected Bale instance.'));
                    }

                    // Check active 'bale-cms' service or god roles
                    $user = User::where('uuid', $value)->first();
                    $hasBaleCmsService = PersonHasService::where('user_uuid', $value)
                        ->whereHas('service', function ($q) {
                            $q->where('slug', 'bale-cms');
                        })
                        ->where('actived', true)
                        ->exists();

                    $isGodRole = $user?->hasAnyRole(['root', 'admin']);

                    if (!$hasBaleCmsService && !$isGodRole) {
                        $fail(__('This user does not have an active Bale CMS service.'));
                    }
                },
            ],
        ];
    }

    public function mount($baleUser = null)
    {
        if ($baleUser) {
            $this->isEdit = true;
            $item = BaleUser::findOrFail($baleUser);

            if ($item->user?->hasRole('root') && !auth()->user()->hasRole('root')) {
                abort(403, __('Only users with root role can edit this assignment.'));
            }

            if (!auth()->user()->can('bale-user.update')) {
                abort(403);
            }

            $this->baleUserId = $item->id;
            $this->bale_id = $item->bale_id;
            $this->user_uuid = $item->user_uuid;
            $this->role = $item->role;
        } else {
            if (!auth()->user()->can('bale-user.create')) {
                abort(403);
            }
        }
    }

    #[Computed]
    public function baleLists()
    {
        return BaleList::orderBy('name')->get();
    }

    #[Computed]
    public function users()
    {
        return User::where(function ($query) {
            $query->whereHas('services', function ($q) {
                $q->where('rakaca_services.slug', 'bale-cms')
                    ->where('person_has_services.actived', true);
            })
                ->orWhereHas('roles', function ($q) {
                    $q->whereIn('name', ['root', 'admin']);
                });
        })->orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        $user = User::where('uuid', $this->user_uuid)->first();

        DB::transaction(function () use ($user) {
            if ($this->isEdit) {
                $item = BaleUser::findOrFail($this->baleUserId);

                $item->update([
                    'bale_id' => $this->bale_id,
                    'user_uuid' => $this->user_uuid,
                    'role' => $this->role,
                ]);

                // Sync to new tenant
                $this->syncUserToTenant($this->bale_id, $user, $this->role);
            } else {
                BaleUser::create([
                    'bale_id' => $this->bale_id,
                    'user_uuid' => $this->user_uuid,
                    'role' => $this->role,
                ]);

                // Sync to tenant
                $this->syncUserToTenant($this->bale_id, $user, $this->role);
            }
        });

        session()->flash('message', $this->isEdit ? __('Bale User updated successfully.') : __('Bale User created successfully.'));

        $this->redirectRoute('rakaca.landlord.bale-user.index', navigate: true);
    }

    protected function syncUserToTenant($baleId, $landlordUser, $role = null)
    {
        try {
            TenantManager::initializeFromBaleUuid($baleId);
            $connection = TenantManager::getActiveConnection();

            // Only Sync User Table
            DB::connection($connection)->table('users')->updateOrInsert(
                ['uuid' => $landlordUser->uuid],
                [
                    'name' => $landlordUser->name,
                    'username' => $landlordUser->username,
                    'role' => $role,
                    'email' => $landlordUser->email,
                    'password' => $landlordUser->password,
                    'updated_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            info("Failed to sync user to tenant: " . $e->getMessage());
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-user.form')
            ->title($this->isEdit ? __('Edit Bale User') : __('Create Bale User'));
    }
}
