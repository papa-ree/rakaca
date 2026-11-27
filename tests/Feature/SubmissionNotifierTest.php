<?php

namespace Paparee\Rakaca\Tests\Feature;

use App\Models\User;
use Bale\Core\Models\UserProfile;
use Bale\Wara\Models\WaraClient;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Support\Str;
use Paparee\Rakaca\Enums\SubmissionStatus;
use Paparee\Rakaca\Models\Form;
use Paparee\Rakaca\Models\RakacaService;
use Paparee\Rakaca\Models\RakacaSubmission;
use Paparee\Rakaca\Notifications\SubmissionStatusChanged;
use Paparee\Rakaca\Services\TicketWorkflowService;

function submissionNotifierFixture(?string $phone = '081234567890'): RakacaSubmission
{
    $service = RakacaService::create([
        'id' => (string) Str::uuid(),
        'name' => 'Layanan Notifikasi',
        'slug' => 'layanan-notifikasi-'.Str::random(6),
        'actived' => true,
    ]);

    $form = Form::create([
        'id' => (string) Str::uuid(),
        'rakaca_service_id' => $service->id,
        'name' => 'Form Notifikasi',
        'slug' => 'form-notifikasi-'.Str::random(6),
        'meta' => ['fields' => []],
        'response_form_schema' => [],
        'actived' => true,
    ]);

    $user = User::factory()->create();

    if ($phone !== null) {
        UserProfile::create([
            'user_uuid' => $user->uuid,
            'phone' => $phone,
        ]);
    }

    return RakacaSubmission::create([
        'user_uuid' => $user->uuid,
        'rakaca_form_id' => $form->id,
        'code' => 'TKT-'.Str::upper(Str::random(8)),
        'status' => SubmissionStatus::MenungguBerkas,
        'items' => [],
    ]);
}

function rakacaWorkflow(RakacaSubmission $submission): TicketWorkflowService
{
    return app(TicketWorkflowService::class, ['submission' => $submission]);
}

beforeEach(function () {
    NotificationFacade::fake();

    config()->set('rakaca.notify.enabled', true);
    config()->set('rakaca.notify.client_name', 'Rakaca (Package)');
    config()->set('rakaca.notify.purpose', 'rakaca');
    config()->set('rakaca.notify.recipients', []);

    WaraClient::query()->create([
        'type' => WaraClient::TYPE_SERVICE,
        'name' => 'Rakaca (Package)',
        'is_active' => true,
    ]);
});

it('submitFiles mengirim ke pemohon dan nomor petugas', function () {
    config()->set('rakaca.notify.recipients', ['081200000001']);

    $submission = submissionNotifierFixture();

    expect(rakacaWorkflow($submission)->submitFiles())->toBeTrue();

    NotificationFacade::assertSentOnDemandTimes(SubmissionStatusChanged::class, 2);
    NotificationFacade::assertSentOnDemand(
        SubmissionStatusChanged::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['wara'] === '6281234567890'
    );
    NotificationFacade::assertSentOnDemand(
        SubmissionStatusChanged::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['wara'] === '6281200000001'
    );
});

it('requestRevision hanya mengirim ke pemohon', function () {
    config()->set('rakaca.notify.recipients', ['081200000001']);

    $submission = submissionNotifierFixture();
    $submission->update(['status' => SubmissionStatus::Diproses]);

    expect(rakacaWorkflow($submission)->requestRevision('Lengkapi KTP.'))->toBeTrue();

    NotificationFacade::assertSentOnDemandTimes(SubmissionStatusChanged::class, 1);
});

it('nomor pemohon kosong mengembalikan false tapi petugas tetap dikirim', function () {
    config()->set('rakaca.notify.recipients', ['081200000001']);

    $submission = submissionNotifierFixture(null);

    expect(rakacaWorkflow($submission)->submitFiles())->toBeFalse();

    NotificationFacade::assertSentOnDemandTimes(SubmissionStatusChanged::class, 1);
    NotificationFacade::assertSentOnDemand(
        SubmissionStatusChanged::class,
        fn ($notification, $channels, $notifiable) => $notifiable->routes['wara'] === '6281200000001'
    );
});

it('client wara yang tidak ada berarti tidak ada kiriman', function () {
    WaraClient::query()->delete();

    $submission = submissionNotifierFixture();

    expect(rakacaWorkflow($submission)->submitFiles())->toBeTrue();

    NotificationFacade::assertNothingSent();
});

it('nomor pemohon yang sama dengan petugas hanya dikirimi sekali', function () {
    config()->set('rakaca.notify.recipients', ['081234567890']);

    $submission = submissionNotifierFixture('081234567890');

    rakacaWorkflow($submission)->submitFiles();

    NotificationFacade::assertSentOnDemandTimes(SubmissionStatusChanged::class, 1);
});

it('sakelar mati berarti tidak ada kiriman dan tidak ada peringatan', function () {
    config()->set('rakaca.notify.enabled', false);

    $submission = submissionNotifierFixture(null);

    expect(rakacaWorkflow($submission)->submitFiles())->toBeTrue();

    NotificationFacade::assertNothingSent();
});

it('pesan penolakan memuat alasan', function () {
    $submission = submissionNotifierFixture();
    $submission->update(['status' => SubmissionStatus::SiapDireview]);

    rakacaWorkflow($submission)->reject('Berkas tidak sesuai ketentuan.');

    NotificationFacade::assertSentOnDemand(
        SubmissionStatusChanged::class,
        fn ($notification, $channels, $notifiable) => str_contains($notification->toWara($notifiable), 'Alasan: Berkas tidak sesuai ketentuan.')
    );
});

it('pesan penyelesaian memuat isian formulir respon admin', function () {
    $submission = submissionNotifierFixture();
    $submission->update(['status' => SubmissionStatus::Diproses]);
    $submission->form->update([
        'response_form_schema' => [
            ['key' => 'nomor_surat', 'label' => 'Nomor Surat', 'type' => 'string'],
            ['key' => 'disetujui', 'label' => 'Disetujui', 'type' => 'checkbox'],
            ['key' => 'kosong', 'label' => 'Tidak Diisi', 'type' => 'string'],
        ],
    ]);

    rakacaWorkflow($submission)->complete([
        'nomor_surat' => 'B-123',
        'disetujui' => true,
    ]);

    NotificationFacade::assertSentOnDemand(
        SubmissionStatusChanged::class,
        function ($notification, $channels, $notifiable) {
            $message = $notification->toWara($notifiable);

            return str_contains($message, 'Nomor Surat: B-123')
                && str_contains($message, 'Disetujui: Ya')
                && ! str_contains($message, 'Tidak Diisi')
                && ! str_contains($message, 'dapat diunduh');
        }
    );
});

it('pesan penyelesaian memberitahu berkas respon dapat diunduh di rakaca', function () {
    $submission = submissionNotifierFixture();
    $submission->update(['status' => SubmissionStatus::Diproses]);
    $submission->form->update([
        'response_form_schema' => [
            ['key' => 'lampiran', 'label' => 'Lampiran', 'type' => 'string'],
        ],
    ]);

    rakacaWorkflow($submission)->complete([
        'lampiran' => 'rakaca-submissions/hasil-surat.pdf',
    ]);

    NotificationFacade::assertSentOnDemand(
        SubmissionStatusChanged::class,
        fn ($notification, $channels, $notifiable) => str_contains(
            $notification->toWara($notifiable),
            'Berkas hasil dapat diunduh di Rakaca.'
        )
    );
});
