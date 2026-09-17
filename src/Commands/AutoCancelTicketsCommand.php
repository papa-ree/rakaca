<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Services\TicketWorkflowService;

class AutoCancelTicketsCommand extends Command
{
    protected $signature = 'rakaca:auto-cancel';

    protected $description = 'Batalkan otomatis tiket menunggu-berkas yang belum difinalisasi dalam 3x24 jam';

    public function handle(): int
    {
        $hours = (int) config('rakaca.ticket.auto_cancel_hours', 72);
        $cutoff = now()->subHours($hours);

        $tickets = RakacaSubmission::query()
            ->where('status', SubmissionStatus::MenungguBerkas->value)
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('status_changed_at')
                    ->where('created_at', '<=', $cutoff)
                    ->orWhere('status_changed_at', '<=', $cutoff);
            })
            ->get();

        $count = 0;

        foreach ($tickets as $ticket) {
            try {
                app(TicketWorkflowService::class, ['submission' => $ticket])->autoCancel();
                $count++;
            } catch (\Throwable) {
                // skip tiket yang transisinya gagal
            }
        }

        $this->info("{$count} tiket dibatalkan otomatis.");

        return self::SUCCESS;
    }
}
