<?php

namespace Paparee\Rakaca\Services;

use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\RakacaFormResponse;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Support\WorkdayCalendar;

final class TicketWorkflowService
{
    public function __construct(
        private RakacaSubmission $submission
    ) {}

    public function submitFiles(): void
    {
        $this->transitionTo(
            SubmissionStatus::SiapDireview,
            function () {
                $this->submission->update(['files_finalized_at' => now()]);

                $this->resumeSla();
                $this->snapshotSla();
            }
        );
    }

    public function takeOver(): void
    {
        $this->transitionTo(SubmissionStatus::Diproses, function () {
            $this->submission->response()->firstOrCreate([
                'rakaca_submission_id' => $this->submission->id,
            ], [
                'processed_at' => now(),
                'processed_by' => auth()->user()->uuid ?? null,
            ]);
        });
    }

    public function reject(string $reason): void
    {
        $this->ensureResponse()->update([
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);

        $this->transitionTo(SubmissionStatus::Ditolak);
    }

    public function requestRevision(string $note): void
    {
        $this->ensureResponse()->update([
            'revise_note' => $note,
            'requested_revision_at' => now(),
        ]);

        if ($this->submission->sla_due_at !== null && $this->submission->sla_paused_at === null) {
            $this->submission->update(['sla_paused_at' => now()]);
        }

        $this->transitionTo(SubmissionStatus::MenungguRevisi);
    }

    public function complete(array $resolutionData): void
    {
        $this->ensureResponse()->update([
            'resolution_data' => $resolutionData,
            'resolved_at' => now(),
            'resolved_by' => auth()->user()->uuid ?? null,
        ]);

        $this->transitionTo(SubmissionStatus::Selesai);
    }

    public function userCancel(): void
    {
        if (auth()->user()?->uuid !== $this->submission->user_uuid) {
            abort(403, 'Hanya pemilik tiket yang bisa membatalkan.');
        }

        $this->ensureResponse()->update([
            'cancelled_reason' => 'user',
        ]);

        $this->transitionTo(SubmissionStatus::Dibatalkan);
    }

    public function autoCancel(): void
    {
        $this->ensureResponse()->update([
            'cancelled_reason' => 'auto-cancel: 3x24 jam (Menunggu Berkas)',
        ]);

        $this->transitionTo(SubmissionStatus::Dibatalkan);
    }

    private function transitionTo(SubmissionStatus $to, ?callable $before = null): void
    {
        $from = $this->submission->status;

        if ($from === null || ! SubmissionStatus::canTransition($from, $to)) {
            abort(403, "Transisi dari [{$from?->value}] ke [{$to->value}] tidak diizinkan.");
        }

        if ($before !== null) {
            $before();
        }

        $this->submission->update(['status' => $to]);
    }

    private function ensureResponse(): RakacaFormResponse
    {
        return $this->submission->response()->firstOrCreate([
            'rakaca_submission_id' => $this->submission->id,
        ]);
    }

    /**
     * Bekukan target SLA layanan pada pengajuan saat berkas final pertama.
     * Pengajuan lama (tanpa SLA) tidak di-backfill.
     */
    private function snapshotSla(): void
    {
        if ($this->submission->sla_started_at !== null) {
            return;
        }

        $days = $this->submission->form?->service?->sla_days;

        if (! $days) {
            return;
        }

        $start = now();

        $this->submission->update([
            'sla_target_days' => $days,
            'sla_started_at' => $start,
            'sla_due_at' => WorkdayCalendar::deadline($start, $days),
        ]);
    }

    /**
     * Lanjutkan SLA setelah revisi: tenggat digeser sebesar jam kerja
     * selama status Menunggu Revisi.
     */
    private function resumeSla(): void
    {
        if ($this->submission->sla_paused_at === null || $this->submission->sla_due_at === null) {
            return;
        }

        $pausedMinutes = WorkdayCalendar::workingMinutes($this->submission->sla_paused_at, now());

        $this->submission->update([
            'sla_due_at' => WorkdayCalendar::addWorkingMinutes($this->submission->sla_due_at, $pausedMinutes),
            'sla_paused_at' => null,
        ]);
    }
}
