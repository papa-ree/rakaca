<?php

namespace Paparee\Rakaca\Livewire\Pages\Landlord\Submission;

use Bale\Core\Support\Sanitize;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Paparee\Rakaca\Models\RakacaSubmission;

#[Layout('rakaca::layouts.app')]
#[Title('Detail Submission')]
class Detail extends Component
{
    public RakacaSubmission $submission;

    public string $admin_response = '';

    public function mount(RakacaSubmission $submission): void
    {
        if (!auth()->user()->can('submission.read')) {
            abort(403);
        }

        $this->submission = $submission->load(['form.service', 'user', 'uploads', 'processedBy']);

        // Tidak otomatis review — review via tombol manual (agar user masih bisa upload saat pending)
        $this->admin_response = $this->submission->admin_response ?? '';
    }

    protected function rules(): array
    {
        return [
            'admin_response' => 'required|string|min:10|max:5000',
        ];
    }

    public function review(): void
    {
        if (!auth()->user()->can('submission.update')) {
            abort(403);
        }

        if ($this->submission->status !== 'pending') {
            abort(403, __('Hanya pengajuan pending yang bisa direview.'));
        }

        $this->submission->update([
            'status' => 'review',
            'processed_at' => now(),
            'processed_by' => auth()->user()->uuid,
        ]);

        $this->dispatch('toast', message: __('Pengajuan diproses (review).'), type: 'success');

        $this->redirectRoute('rakaca.landlord.submission.index', navigate: true);
    }

    public function approve(): void
    {
        if (!auth()->user()->can('submission.update')) {
            abort(403);
        }

        if ($this->submission->status !== 'review') {
            abort(403, __('Hanya pengajuan yang sedang direview yang bisa disetujui.'));
        }

        $this->admin_response = Sanitize::text($this->admin_response);
        $this->validate();

        $this->submission->update([
            'status' => 'approved',
            'admin_response' => $this->admin_response,
            'processed_at' => now(),
            'processed_by' => auth()->user()->uuid,
        ]);

        $this->dispatch('toast', message: __('Pengajuan disetujui.'), type: 'success');

        $this->redirectRoute('rakaca.landlord.submission.index', navigate: true);
    }

    public function reject(): void
    {
        if (!auth()->user()->can('submission.update')) {
            abort(403);
        }

        if (!in_array($this->submission->status, ['pending', 'review'])) {
            abort(403, __('Hanya pengajuan pending atau review yang bisa ditolak.'));
        }

        $this->admin_response = Sanitize::text($this->admin_response);
        $this->validate();

        $this->submission->update([
            'status' => 'rejected',
            'admin_response' => $this->admin_response,
            'processed_at' => now(),
            'processed_by' => auth()->user()->uuid,
        ]);

        $this->dispatch('toast', message: __('Pengajuan ditolak.'), type: 'success');

        $this->redirectRoute('rakaca.landlord.submission.index', navigate: true);
    }

    public function close(): void
    {
        if (!auth()->user()->can('submission.update')) {
            abort(403);
        }

        if ($this->submission->status === 'ditutup') {
            abort(403, __('Pengajuan sudah ditutup.'));
        }

        $this->admin_response = Sanitize::text($this->admin_response);
        $this->validate();

        $this->submission->update([
            'status' => 'ditutup',
            'admin_response' => $this->admin_response,
            'processed_at' => now(),
            'processed_by' => auth()->user()->uuid,
        ]);

        $this->dispatch('toast', message: __('Pengajuan ditutup.'), type: 'success');

        $this->redirectRoute('rakaca.landlord.submission.index', navigate: true);
    }

    public function back(): void
    {
        $this->redirectRoute('rakaca.landlord.submission.index', navigate: true);
    }

    public function render()
    {
        return view('rakaca::livewire.pages.landlord.submission.detail');
    }
}
