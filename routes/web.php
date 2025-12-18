<?php

use Illuminate\Support\Facades\Route;
use Paparee\Rakaca\Livewire\Pages\Guest\Index;
use Paparee\Rakaca\Livewire\Pages\Landlord\Dashboard\Index as LandlordDashboardIndex;

Route::middleware(['web', 'auth'])->as('rakaca.')->group(function () {

    Route::group(['middleware' => ['permission:dashboard']], function () {
        Route::get('landlord-dashboard', LandlordDashboardIndex::class)->name('landlord-dashboard.index');
    });

    // redirect from dashboard redirector in core package
    Route::group(['middleware' => ['permission:waiting room']], function () {
        Route::get('guest', Index::class)->name('overview');
    });
});
