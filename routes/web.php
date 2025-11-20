<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Paparee\BalePraban\Livewire\Pages\Dashboard\Index as DashboardIndex;
use Paparee\BalePraban\Livewire\Pages\SelectPage\Index as SelectPageIndex;
use Paparee\BalePraban\Livewire\SharedComponents\ExitCms;
use Paparee\BalePraban\Middleware\EnsureBaleSelected;
use Paparee\BalePraban\Middleware\SwitchBaleConnection;
use Paparee\Rakaca\Livewire\Pages\Guest\Index;

Route::middleware(['web', 'auth'])->as('rakaca.')->group(function () {

    Route::group(['middleware' => ['permission:waiting room']], function () {
        Route::get('guest', Index::class)->name('overview');
    });
});
