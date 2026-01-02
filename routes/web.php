<?php

use Illuminate\Support\Facades\Route;
use Paparee\Rakaca\Livewire\LandingPages\Home\Index;
use Paparee\Rakaca\Livewire\Pages\Guest\SelectBale\Index as SelectBaleIndex;
use Paparee\Rakaca\Livewire\Pages\Landlord\Dashboard\Index as LandlordDashboardIndex;
use Paparee\Rakaca\Livewire\Pages\Guest\Dashboard\Index as GuestDashboardIndex;

Route::middleware(['web'])->group(function () {

    Route::get('/', Index::class);

    Route::middleware(['auth'])->as('rakaca.')->group(function () {

        Route::group(['middleware' => ['permission:dashboard']], function () {
            Route::get('landlord-dashboard', LandlordDashboardIndex::class)->name('landlord-dashboard.index');
        });

        // redirect from dashboard redirector in core package
        Route::group(['middleware' => ['permission:waiting room']], function () {
            Route::get('guest', GuestDashboardIndex::class)->name('guest.dashboard');
        });

        // select bale
        Route::get('select-bale', SelectBaleIndex::class)->name('select-bale');

    });
});
