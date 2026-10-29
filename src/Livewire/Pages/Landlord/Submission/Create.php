<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Create as GuestCreate;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaSubmission;

/**
 * Tambah pengajuan manual oleh admin, atas nama user terdaftar.
 *
 * Seluruh validasi skema dinamis, upload, dan sanitasi diwarisi dari halaman
 * guest; yang berbeda hanya pemilik pengajuan dan tujuan setelah simpan.
 */
#[Layout('rakaca::layouts.app')]
#[Title('Tambah Pengajuan')]
class Create extends GuestCreate
{
    public string $user_uuid = '';

    public string $userSearch = '';

    public function mount(): void
    {
        if (! auth()->user()->can('submission.create')) {
            abort(403);
        }

        parent::mount();
    }

    protected function rules(): array
    {
        return array_merge(parent::rules(), [
            'user_uuid' => 'required|uuid|exists:users,uuid',
        ]);
    }

    protected function validationAttributes(): array
    {
        return array_merge(parent::validationAttributes(), [
            'user_uuid' => __('Pemohon'),
        ]);
    }

    protected function applicantUuid(): string
    {
        return $this->user_uuid;
    }

    protected function afterSave(RakacaSubmission $submission): void
    {
        session()->flash('success', 'Pengajuan berhasil dibuat.');

        $this->redirectRoute('rakaca.landlord.submission.detail', $submission->id, navigate: true);
    }

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->userSearch !== '', function ($query): void {
                $term = '%'.$this->userSearch.'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->orderBy('name')
            ->limit(50)
            ->get(['uuid', 'name', 'email']);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.create', [
            'forms' => Form::with('service')->where('actived', true)->get(),
            'userOptions' => $this->users
                ->map(fn (User $user): array => [
                    'uuid' => $user->uuid,
                    'name' => $user->name.' — '.$user->email,
                ])
                ->all(),
        ]);
    }
}
