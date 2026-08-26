<?php

use JustBetter\StatamicStarterKit\Http\Controllers\CP\GlobalComponentController;
use Illuminate\Support\Facades\Route;

Route::post('global-components/convert', GlobalComponentController::class)
    ->name('global-components.convert');
