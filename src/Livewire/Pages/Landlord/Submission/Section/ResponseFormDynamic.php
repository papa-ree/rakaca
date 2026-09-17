<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Section;

use Livewire\Component;

class ResponseFormDynamic extends Component
{
    public array $schema = [];

    /**
     * `resolution` di sini adalah property yang di-entangle langsung ke parent (Detail).
     * Child TIDAK memegang state sendiri — semua perubahan otomatis masuk ke parent.
     */
    public array $resolution = [];

    public function mount(array $schema = [], array $resolution = []): void
    {
        $this->schema = $schema;
        $this->resolution = $resolution;
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.section.response-form-dynamic');
    }
}
