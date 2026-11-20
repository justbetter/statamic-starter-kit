<?php

use Illuminate\Support\Facades\Route;
use JustBetter\StatamicStarterKit\Http\Controllers\CP\CachesController;
use JustBetter\StatamicStarterKit\Http\Controllers\CP\GlobalComponentController;

Route::post('global-components/convert', GlobalComponentController::class)
    ->name('global-components.convert');

Route::post('caches/clear-application', [CachesController::class, 'clearApplication'])
    ->name('justbetter.caches.clear-application');

Route::post('caches/clear-static', [CachesController::class, 'clearStatic'])
    ->name('justbetter.caches.clear-static');
