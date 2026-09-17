<?php

use Illuminate\Support\Facades\Route;
use Paparee\Rakaca\Http\Controllers\Api\V1\FormController;
use Paparee\Rakaca\Http\Controllers\Api\V1\SubmissionController;

/*
|--------------------------------------------------------------------------
| API Rakaca (v1)
|--------------------------------------------------------------------------
|
| Dimuat oleh RakacaServiceProvider. Middleware group `bale.api` disediakan
| oleh bale/api; scope per endpoint didaftarkan lewat registerApiScopes().
|
*/

Route::middleware('bale.api')->prefix('api/v1/rakaca')->name('api.rakaca.v1.')->group(function () {
    Route::get('forms', [FormController::class, 'index'])
        ->middleware('scope:rakaca.form.read')
        ->name('forms.index');

    Route::get('forms/{form}', [FormController::class, 'show'])
        ->middleware('scope:rakaca.form.read')
        ->name('forms.show');

    Route::post('submissions', [SubmissionController::class, 'store'])
        ->middleware('scope:rakaca.submission.write')
        ->name('submissions.store');

    Route::get('submissions', [SubmissionController::class, 'index'])
        ->middleware('scope:rakaca.submission.read')
        ->name('submissions.index');
});
