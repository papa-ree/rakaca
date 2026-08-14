<?php

use Illuminate\Support\Facades\Route;
use Paparee\Rakaca\Livewire\Pages\Guest\Aduan\Index as AduanIndex;
use Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Index as GuestDashboardIndex;
use Paparee\Rakaca\Livewire\Pages\Guest\SelectBale\Index as SelectBaleIndex;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Create as GuestSubmissionCreate;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Edit as GuestSubmissionEdit;
use Paparee\Rakaca\Livewire\Pages\Guest\Submission\Index as GuestSubmissionIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Form;
use Paparee\Rakaca\Livewire\Pages\Landlord\Dashboard\Index as LandlordDashboardIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\Form\Form as RakacaForm;
use Paparee\Rakaca\Livewire\Pages\Landlord\Form\Index as RakacaFormIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\PersonalService\Create as PersonalServiceCreate;
use Paparee\Rakaca\Livewire\Pages\Landlord\PersonalService\Edit as PersonalServiceEdit;
use Paparee\Rakaca\Livewire\Pages\Landlord\PersonalService\Index as PersonalServiceIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\Service\Form as RakacaServiceForm;
use Paparee\Rakaca\Livewire\Pages\Landlord\Service\Index as RakacaServiceIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Create;
use Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Edit;
use Paparee\Rakaca\Livewire\Pages\Landlord\Submission\Index;

Route::middleware(['web'])->group(function () {

    // Public: Aduan / Bantuan (tanpa login)
    Route::get('bantuan', AduanIndex::class)
        ->middleware('throttle:30,1')
        ->name('rakaca.aduan.index');

    Route::middleware(['auth'])->as('rakaca.')->group(function () {

        Route::group(['middleware' => ['permission:dashboard']], function () {
            Route::get('landlord-dashboard', LandlordDashboardIndex::class)->name('landlord-dashboard.index');

            // Service Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:service.read']], function () {
                Route::get('services', RakacaServiceIndex::class)->name('landlord.service.index');
                Route::get('services/create', RakacaServiceForm::class)->name('landlord.service.create');
                Route::get('services/{service}/edit', RakacaServiceForm::class)->name('landlord.service.edit');
            });

            // Form Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:form.read']], function () {
                Route::get('forms', RakacaFormIndex::class)->name('landlord.form.index');
                Route::get('forms/create', RakacaForm::class)->name('landlord.form.create');
                Route::get('forms/{form}/edit', RakacaForm::class)->name('landlord.form.edit');
            });

            // Submission Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:submission.read']], function () {
                Route::get('submissions', Index::class)->name('landlord.submission.index');
                Route::get('submissions/create', Create::class)->name('landlord.submission.create');
                Route::get('submissions/{submission}/edit', Edit::class)->name('landlord.submission.edit');
            });

            // Personal Service Management (Customer)
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:personal-service.read']], function () {
                Route::get('personal-services', PersonalServiceIndex::class)->name('landlord.personal-service.index');
                Route::get('personal-services/create', PersonalServiceCreate::class)->name('landlord.personal-service.create');
                Route::get('personal-services/{personalService}/edit', PersonalServiceEdit::class)->name('landlord.personal-service.edit');
            });

            // Organization Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:organization.read']], function () {
                Route::get('organizations', Paparee\Rakaca\Livewire\Pages\Landlord\Organization\Index::class)->name('landlord.organization.index');
                Route::get('organizations/create', Paparee\Rakaca\Livewire\Pages\Landlord\Organization\Create::class)->name('landlord.organization.create');
                Route::get('organizations/{organization}/edit', Paparee\Rakaca\Livewire\Pages\Landlord\Organization\Edit::class)->name('landlord.organization.edit');
            });

            // Bale List Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:bale-list.read']], function () {
                Route::get('bale-lists', Paparee\Rakaca\Livewire\Pages\Landlord\BaleList\Index::class)->name('landlord.bale-list.index');
                Route::get('bale-lists/create', Paparee\Rakaca\Livewire\Pages\Landlord\BaleList\Create::class)->name('landlord.bale-list.create');
                Route::get('bale-lists/{baleList}/edit', Paparee\Rakaca\Livewire\Pages\Landlord\BaleList\Edit::class)->name('landlord.bale-list.edit');
            });

            // Bale User Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:bale-user.read']], function () {
                Route::get('bale-users', Paparee\Rakaca\Livewire\Pages\Landlord\BaleUser\Index::class)->name('landlord.bale-user.index');
                Route::get('bale-users/create', Form::class)->name('landlord.bale-user.create');
                Route::get('bale-users/{baleUser}/edit', Form::class)->name('landlord.bale-user.edit');
            });

            // Analytic Management
            Route::group(['prefix' => 'rakaca', 'middleware' => ['permission:analytic.read']], function () {
                Route::get('analytics', Paparee\Rakaca\Livewire\Pages\Landlord\Analytic\Index::class)->name('landlord.analytic.index');
                Route::get('analytics/create', Paparee\Rakaca\Livewire\Pages\Landlord\Analytic\Create::class)->name('landlord.analytic.create');
                Route::get('analytics/{analytic}/edit', Paparee\Rakaca\Livewire\Pages\Landlord\Analytic\Edit::class)->name('landlord.analytic.edit');
            });

        });

        // redirect from dashboard redirector in core package
        Route::group(['middleware' => ['permission:guest.dashboard']], function () {
            Route::get('guest', GuestDashboardIndex::class)->name('guest-dashboard.index');
        });

        // select bale
        Route::group(['middleware' => ['permission:select-bale']], function () {
            Route::get('select-bale', SelectBaleIndex::class)->name('select-bale');
        });

        // Guest Submission Management
        Route::group(['prefix' => 'submissions', 'as' => 'guest.submission.'], function () {
            Route::get('/', GuestSubmissionIndex::class)->name('index');
            Route::get('/create', GuestSubmissionCreate::class)->name('create');
            Route::get('/{submission}/edit', GuestSubmissionEdit::class)->name('edit');
        });

    });
});
