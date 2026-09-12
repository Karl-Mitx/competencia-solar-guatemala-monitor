<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FarmController;
use App\Http\Controllers\GenerationController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProjectionController;
use App\Http\Controllers\ReportEmailController;
use App\Http\Controllers\SolarHelpController;
use App\Http\Controllers\AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/map', [DashboardController::class, 'map'])->name('map');
Route::get('/simulator', \App\Http\Controllers\SimulationController::class)->name('simulator');
Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
Route::get('/reports/print', [DashboardController::class, 'reportPrint'])->name('reports.print');
Route::get('/reports/export', [DashboardController::class, 'export'])->name('reports.export');
Route::post('/reports/email', ReportEmailController::class)->middleware('throttle:3,10')->name('reports.email');
Route::get('/alerts', [DashboardController::class, 'alerts'])->name('alerts.index');
Route::get('/audit', [\App\Http\Controllers\AuditController::class, 'index'])->middleware(['auth', 'asset.manager'])->name('audit.index');
Route::get('/admin/users', [AdminUserController::class, 'index'])->middleware(['auth', 'admin'])->name('admin.users');
Route::view('/api-docs', 'api-docs')->name('api.docs');
Route::view('/manual', 'manual')->name('manual');
Route::post('/solar-help', SolarHelpController::class)->middleware('throttle:10,1')->name('solar-help');
Route::get('/login', [AuthController::class, 'create'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->middleware('guest')->name('password.update');
Route::middleware(['auth', 'asset.manager'])->group(function () {
    Route::resource('farms', FarmController::class)->except(['index', 'show']);
    Route::resource('panels', PanelController::class)->except(['index', 'show', 'destroy']);
    Route::resource('generations', GenerationController::class)->except(['index', 'show', 'destroy']);
    Route::post('/projections', [ProjectionController::class, 'store'])->name('projections.store');
    Route::patch('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
});
Route::get('/farms/compare', [FarmController::class, 'compare'])->name('farms.compare');
Route::resource('farms', FarmController::class)->only(['index', 'show']);
Route::resource('panels', PanelController::class)->only('index');
Route::resource('generations', GenerationController::class)->only('index');
Route::get('/projections', [ProjectionController::class, 'index'])->name('projections.index');
