<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProjectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/map', [DashboardController::class, 'map'])->name('map');
Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
Route::get('/reports/export', [DashboardController::class, 'export'])->name('reports.export');
Route::post('/reports/email', \App\Http\Controllers\ReportEmailController::class)->middleware('throttle:3,10')->name('reports.email');
Route::get('/alerts', [DashboardController::class, 'alerts'])->name('alerts.index');
Route::view('/api-docs', 'api-docs')->name('api.docs');
Route::view('/manual', 'manual')->name('manual');
Route::get('/login', [AuthController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function () {
    Route::resource('farms', FarmController::class)->except(['index', 'show']);
    Route::resource('panels', PanelController::class)->except(['index', 'show', 'destroy']);
    Route::resource('generations', GenerationController::class)->except(['index', 'show', 'destroy']);
    Route::post('/projections', [ProjectionController::class, 'store'])->name('projections.store');
});
Route::resource('farms', FarmController::class)->only(['index', 'show']);
Route::resource('panels', PanelController::class)->only('index');
Route::resource('generations', GenerationController::class)->only('index');
Route::get('/projections', [ProjectionController::class, 'index'])->name('projections.index');
