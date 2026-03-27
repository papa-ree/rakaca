<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\BaleList;

use Livewire\Component;
use Livewire\Attributes\{Layout, Title, Computed};
use Paparee\Rakaca\Models\BaleList;
use Paparee\Rakaca\Models\Organization;
use Illuminate\Support\Str;

#[Layout('rakaca::layouts.app')]
#[Title('Create Bale List')]
class Create extends Component
{
    public $organization_id;
    public $name;
    public $slug;
    public $database_host = 'localhost';
    public $database_name;
    public $database_username = 'root';
    public $database_password;
    public $storage_prefix;
    public $is_active = true;

    protected $rules = [
        'organization_id' => 'required|exists:bale_organizations,id',
        'name' => 'required|min:3|max:255',
        'slug' => 'required|unique:bale_lists,slug',
        'database_host' => 'required',
        'database_name' => 'required|unique:bale_lists,database_name',
        'database_username' => 'required',
        'database_password' => 'nullable',
        'is_active' => 'boolean',
    ];

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
        if (empty($this->database_name)) {
            $this->database_name = 'bale_' . str_replace('-', '_', $this->slug);
        }
    }

    public function mount()
    {
        if (!auth()->user()->can('bale-list.create')) {
            abort(403);
        }
    }

    #[Computed]
    public function organizations()
    {
        return Organization::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        BaleList::create([
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

        session()->flash('message', __('Bale List created successfully.'));

        return redirect()->route('rakaca.landlord.bale-list.index');
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.bale-list.create');
    }
}
