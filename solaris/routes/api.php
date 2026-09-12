<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:api')->group(function () {
    Route::get('departments', [ApiController::class, 'departments']);
    Route::get('farms', [ApiController::class, 'farms']);
    Route::get('farms/compare', [ApiController::class, 'compareFarms']);
    Route::get('farms/{farm}', [ApiController::class, 'farm']);
    Route::get('generations', [ApiController::class, 'generations']);
    Route::get('statistics', [ApiController::class, 'statistics']);
});
