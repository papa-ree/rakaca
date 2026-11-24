<?php

namespace Paparee\Rakaca\Commands;

use Illuminate\Console\Command;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Services\TicketWorkflowService;

class AutoCancelTicketsCommand extends Command
{
    protected $signature = 'rakaca:auto-cancel';

    protected $description = 'Batalkan otomatis tiket menunggu-berkas/menunggu-revisi tanpa update dalam 3x24 jam';

    public function handle(): int
    {
        $hours = (int) config('rakaca.ticket.auto_cancel_hours', 72);
        $cutoff = now()->subHours($hours);

        $tickets = RakacaSubmission::query()
            ->whereIn('status', [SubmissionStatus::MenungguBerkas->value, SubmissionStatus::MenungguRevisi->value])
            // updated_at bergerak saat pemohon menyimpan draf, jadi "tidak ada update" = tidak ada save sama sekali.
            ->where('updated_at', '<=', $cutoff)
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
