<?php

namespace Paparee\Rakaca\Notifications;

use Bale\Wara\Models\WaraClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;

class SubmissionStatusChanged extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public RakacaSubmission $submission) {}

    public function via(object $notifiable): array
    {
        return ['wara'];
    }

    public function toWara(object $notifiable): string
    {
        $submission = $this->submission;
        $status = $submission->status;

        $lines = [
            sprintf('Pengajuan %s (%s)', $submission->code, $submission->form?->service?->name ?? '-'),
            'Status: '.($status?->label() ?? '-'),
            'Pemohon: '.($submission->user?->name ?? '-'),
        ];

        if ($status === SubmissionStatus::MenungguRevisi && filled($submission->response?->revise_note)) {
            $lines[] = 'Catatan: '.$submission->response->revise_note;
        }

        if ($status === SubmissionStatus::Ditolak && filled($submission->response?->rejection_reason)) {
            $lines[] = 'Alasan: '.$submission->response->rejection_reason;
        }

        return implode("\n", $lines);
    }

    public function waraClient(): ?WaraClient
    {
        return WaraClient::query()
            ->where('name', (string) config('rakaca.notify.client_name'))
            ->where('is_active', true)
            ->first();
    }

    public function waraPurpose(): ?string
    {
        return (string) config('rakaca.notify.purpose', 'rakaca');
    }
}
