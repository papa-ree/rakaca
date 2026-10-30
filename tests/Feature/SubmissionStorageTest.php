<?php

namespace Paparee\Rakaca\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Paparee\Rakaca\Support\SubmissionStorage;

it('memakai disk public di luar production', function () {
    app()['env'] = 'local';

    expect(SubmissionStorage::disk())->toBe('public');
});

it('memakai disk s3 di production', function () {
    app()['env'] = 'production';

    expect(SubmissionStorage::disk())->toBe('s3');
});

it('membuat url berkas sesuai disk aktif dan null untuk path kosong', function () {
    Storage::fake('public');
    app()['env'] = 'local';

    Storage::disk('public')->put('rakaca-submission-uploads/a.pdf', 'x');

    expect(SubmissionStorage::url('rakaca-submission-uploads/a.pdf'))
        ->toContain('rakaca-submission-uploads/a.pdf')
        ->and(SubmissionStorage::url(null))->toBeNull()
        ->and(SubmissionStorage::url(''))->toBeNull();
});
