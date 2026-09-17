<?php

namespace Paparee\Rakaca\Tests\Feature\Enums;

use Paparee\Rakaca\Enums\SubmissionStatus;

it('memiliki 7 status dengan value yang sesuai', function () {
    expect(SubmissionStatus::cases())->toHaveCount(7);

    expect(SubmissionStatus::MenungguBerkas->value)->toBe('menunggu-berkas')
        ->and(SubmissionStatus::SiapDireview->value)->toBe('siap-direview')
        ->and(SubmissionStatus::Diproses->value)->toBe('diproses')
        ->and(SubmissionStatus::MenungguRevisi->value)->toBe('menunggu-revisi')
        ->and(SubmissionStatus::Selesai->value)->toBe('selesai')
        ->and(SubmissionStatus::Dibatalkan->value)->toBe('dibatalkan')
        ->and(SubmissionStatus::Ditolak->value)->toBe('ditolak');
});

it('label dan color tersedia untuk setiap status', function () {
    foreach (SubmissionStatus::cases() as $status) {
        expect($status->label())->toBeString()->not->toBeEmpty();
        expect($status->color())->toBeString()->not->toBeEmpty();
    }
});

it('cancellableByUser hanya untuk status yang diizinkan', function () {
    expect(SubmissionStatus::MenungguBerkas->cancellableByUser())->toBeTrue()
        ->and(SubmissionStatus::SiapDireview->cancellableByUser())->toBeTrue()
        ->and(SubmissionStatus::Diproses->cancellableByUser())->toBeFalse()
        ->and(SubmissionStatus::MenungguRevisi->cancellableByUser())->toBeFalse()
        ->and(SubmissionStatus::Selesai->cancellableByUser())->toBeFalse()
        ->and(SubmissionStatus::Dibatalkan->cancellableByUser())->toBeFalse()
        ->and(SubmissionStatus::Ditolak->cancellableByUser())->toBeFalse();
});

it('allowedTransitions sesuai state machine', function () {
    expect(SubmissionStatus::allowedTransitions(SubmissionStatus::MenungguBerkas))
        ->toBe([SubmissionStatus::SiapDireview, SubmissionStatus::Ditolak, SubmissionStatus::Dibatalkan]);

    expect(SubmissionStatus::allowedTransitions(SubmissionStatus::SiapDireview))
        ->toBe([SubmissionStatus::Diproses, SubmissionStatus::Ditolak, SubmissionStatus::Dibatalkan]);

    expect(SubmissionStatus::allowedTransitions(SubmissionStatus::Diproses))
        ->toBe([SubmissionStatus::MenungguRevisi, SubmissionStatus::Selesai, SubmissionStatus::Ditolak]);

    expect(SubmissionStatus::allowedTransitions(SubmissionStatus::MenungguRevisi))
        ->toBe([SubmissionStatus::SiapDireview, SubmissionStatus::Ditolak]);

    expect(SubmissionStatus::allowedTransitions(SubmissionStatus::Selesai))->toBe([])
        ->and(SubmissionStatus::allowedTransitions(SubmissionStatus::Ditolak))->toBe([])
        ->and(SubmissionStatus::allowedTransitions(SubmissionStatus::Dibatalkan))->toBe([]);
});

it('canTransition mengecek transisi legal dan illegal', function () {
    expect(SubmissionStatus::canTransition(SubmissionStatus::MenungguBerkas, SubmissionStatus::SiapDireview))->toBeTrue()
        ->and(SubmissionStatus::canTransition(SubmissionStatus::MenungguBerkas, SubmissionStatus::Diproses))->toBeFalse()
        ->and(SubmissionStatus::canTransition(SubmissionStatus::Diproses, SubmissionStatus::Selesai))->toBeTrue()
        ->and(SubmissionStatus::canTransition(SubmissionStatus::Diproses, SubmissionStatus::MenungguRevisi))->toBeTrue()
        ->and(SubmissionStatus::canTransition(SubmissionStatus::SiapDireview, SubmissionStatus::Diproses))->toBeTrue()
        ->and(SubmissionStatus::canTransition(SubmissionStatus::Selesai, SubmissionStatus::Ditolak))->toBeFalse();
});

it('fromLegacy memetakan status legacy ke enum terdekat', function () {
    expect(SubmissionStatus::fromLegacy('pending'))->toBe(SubmissionStatus::MenungguBerkas)
        ->and(SubmissionStatus::fromLegacy('review'))->toBe(SubmissionStatus::SiapDireview)
        ->and(SubmissionStatus::fromLegacy('approved'))->toBe(SubmissionStatus::Selesai)
        ->and(SubmissionStatus::fromLegacy('rejected'))->toBe(SubmissionStatus::Ditolak)
        ->and(SubmissionStatus::fromLegacy('ditutup'))->toBe(SubmissionStatus::Dibatalkan)
        ->and(SubmissionStatus::fromLegacy('menunggu-berkas'))->toBe(SubmissionStatus::MenungguBerkas)
        ->and(SubmissionStatus::fromLegacy('siap-direview'))->toBe(SubmissionStatus::SiapDireview)
        ->and(SubmissionStatus::fromLegacy('acak'))->toBeNull();
});
