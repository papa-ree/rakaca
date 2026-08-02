<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleList;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\BaleList;
use Paparee\Rakaca\Models\Organization;

#[Layout('rakaca::layouts.app')]
#[Title('Edit Bale List')]
class Edit extends Component
{
    public $baleListId;

    public $organization_id;

    public $name;

    public $slug;

    public $database_host;

    public $database_name;

    public $database_username;

    public $database_password;

    public $storage_prefix;

    public $is_active;

    protected function rules()
    {
        return [
            'organization_id' => 'required|exists:bale_organizations,id',
            'name' => 'required|min:3|max:255',
            'slug' => 'required|unique:bale_lists,slug,'.$this->baleListId,
            'database_host' => 'required',
            'database_name' => 'required|unique:bale_lists,database_name,'.$this->baleListId,
            'database_username' => 'required',
            // 'database_password' => 'nullable',
            'is_active' => 'boolean',
        ];
    }

    public function mount($baleList)
    {
        if (! auth()->user()->can('bale-list.update')) {
            abort(403);
        }

        $item = BaleList::findOrFail($baleList);
        $this->baleListId = $item->id;
        $this->organization_id = $item->organization_id;
        $this->name = $item->name;
        $this->slug = $item->slug;
        $this->database_host = $item->database_host;
        $this->database_name = $item->database_name;
        $this->database_username = $item->database_username;
        $this->database_password = $item->database_password;
        $this->storage_prefix = $item->storage_prefix;
        $this->is_active = $item->is_active;
    }

    #[Computed]
    public function organizations()
    {
        return Organization::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        $item = BaleList::findOrFail($this->baleListId);
        $item->update([
            'organization_id' => $this->organization_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'database_host' => $this->database_host,
            'database_name' => $this->database_name,
            'database_username' => $this->database_username,
            'database_password' => $this->database_password ?? '',
            'storage_prefix' => $this->storage_prefix,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', __('Bale List updated successfully.'));

        return redirect()->route('rakaca.landlord.bale-list.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-list.edit');
    }
}
