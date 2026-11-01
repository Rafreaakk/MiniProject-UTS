<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;

Route::redirect('/', '/login');

// Belum login (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Sudah login AUTH (Pelapor, Pemantau dan Koordinator)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/{report}/evidence', [ReportController::class, 'evidence'])
        ->name('reports.evidence');

    Route::get('/recap', [ReportController::class, 'recap'])
        ->middleware('can:view-recap')
        ->name('reports.recap');

    Route::patch('/reports/{report}/review', [ReportController::class, 'review'])
        ->middleware('can:review-reports')
        ->name('reports.review');
    
    Route::patch('/reports/{report}/forward', [ReportController::class, 'forward'])
        ->middleware('can:record-forwarding')
        ->name('reports.forward');

    // can:submit-reports (Gate Pelapor)
        Route::middleware('can:submit-reports')->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])
            ->name('reports.create');

        Route::post('/reports', [ReportController::class, 'store'])
            ->name('reports.store');
        
        Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])
            ->name('reports.edit');
        
        Route::put('/reports/{report}', [ReportController::class, 'update'])
            ->name('reports.update');

        Route::get('/schools/{school}/distributions', [ReportController::class, 'distributionsForSchool'])
            ->name('schools.distributions');
    });
});