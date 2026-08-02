<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Form;

use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\Form;

#[Layout('rakaca::layouts.app')]
#[Title('Form Management')]
class Index extends Component
{
    public function mount()
    {
        if (! auth()->user()->can('form.read')) {
            abort(403);
        }
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.form.index');
    }

    #[On('deleteItem')]
    public function deleteForm($id)
    {
        if (! auth()->user()->can('form.delete')) {
            abort(403);
        }

        $form = Form::findOrFail($id);
        $form->delete();

        $this->dispatch('toast', message: 'Form deleted successfully.', type: 'success');
        $this->dispatch('paginated');
    }
}