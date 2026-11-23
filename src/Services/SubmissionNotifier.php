<?php

namespace Paparee\Rakaca\Services;

use Bale\Core\Models\UserProfile;
use Bale\Wara\Models\WaraClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Notifications\SubmissionStatusChanged;
use Throwable;

final class SubmissionNotifier
{
    /**
     * Kirim kabar status ke pemohon, dan ke nomor petugas saat pengajuan masuk.
     *
     * @return bool false hanya bila nomor pemohon kosong.
     */
    public function notify(RakacaSubmission $submission, bool $includeOfficers = false): bool
    {
        if (! config('rakaca.notify.enabled')) {
            return true;
        }

        $client = WaraClient::query()
            ->where('name', (string) config('rakaca.notify.client_name'))
            ->where('is_active', true)
            ->first();

        if ($client === null) {
            Log::warning('Rakaca: client Wara notifikasi tidak ditemukan atau nonaktif.', [
                'client_name' => config('rakaca.notify.client_name'),
                'submission' => $submission->code,
            ]);

            return true;
        }

        $applicant = UserProfile::normalize($submission->user?->profile?->phone);

        $recipients = [];

        if ($applicant !== null) {
            $recipients[] = $applicant;
        }

        if ($includeOfficers) {
            foreach ((array) config('rakaca.notify.recipients', []) as $phone) {
                $normalized = UserProfile::normalize($phone);

                if ($normalized !== null) {
                    $recipients[] = $normalized;
                }
            }
        }

        $recipients = array_unique($recipients);

        if ($recipients !== []) {
            try {
                $notification = new SubmissionStatusChanged($submission);

                foreach ($recipients as $phone) {
                    Notification::route('wara', $phone)->notify($notification);
                }
            } catch (Throwable $e) {
                Log::warning('Rakaca: gagal mengirim notifikasi WhatsApp.', [
                    'submission' => $submission->code,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $applicant !== null;
    }
}
