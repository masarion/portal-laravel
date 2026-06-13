<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\ProjectController;

// スタッフ提出画面（認証不要）
Route::get('/shift/submit/{token}',       [ShiftController::class, 'show'])->name('shift.submit');
Route::get('/shift/submit/{token}/auth',  [ShiftController::class, 'authForm'])->name('shift.auth');
Route::post('/shift/submit/{token}/auth', [ShiftController::class, 'authStore'])->name('shift.auth.store');
Route::post('/shift/submit/{token}/save', [ShiftController::class, 'save'])->name('shift.save');

// 管理画面（認証必須）
Route::middleware(['auth'])->prefix('shift')->name('shift.')->group(function () {
    Route::get('/',                                    [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', ProjectController::class);
    Route::get('projects/{project}/submissions',       [ProjectController::class, 'submissions'])->name('projects.submissions');
});

// 認証ルート
require __DIR__.'/auth.php';

// ルート
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('shift.dashboard')
        : redirect()->route('login');
});
