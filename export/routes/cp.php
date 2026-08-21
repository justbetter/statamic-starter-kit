<?php

use App\Http\Controllers\Cp\GlobalComponentController;
use Illuminate\Support\Facades\Route;

Route::post('global-components/convert', GlobalComponentController::class)
    ->name('global-components.convert');
