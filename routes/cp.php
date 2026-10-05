<?php

use Illuminate\Support\Facades\Route;
use JustBetter\StatamicStarterKit\Http\Controllers\CP\GlobalComponentController;

Route::post('global-components/convert', GlobalComponentController::class)
    ->name('global-components.convert');
