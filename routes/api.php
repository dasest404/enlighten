<?php

use Illuminate\Support\Facades\Route;
use Styde\Enlighten\Http\Controllers\SearchController;

Route::prefix('enlighten/api')->group(function () {
    Route::get('run/{run}/search', SearchController::class)->name('enlighten.api.search');
});
