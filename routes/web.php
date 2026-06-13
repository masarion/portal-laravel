<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\Management\DashboardController;
use App\Http\Controllers\Management\ProjectController;
use App\Http\Controllers\Management\AttendanceDashboardController;
use App\Http\Controllers\Management\EmployeeController;
use App\Http\Controllers\Management\WorkplaceController;
use App\Http\Controllers\Management\AttendanceController;

// ===== トップページ =====
Route::get('/', function () {
    return auth()->check()
        ? view('home')
        : redirect()->route('login');
})->name('home');

// ===== シフト管理：スタッフ提出画面（認証不要） =====
Route::get('/shift/submit/{token}',       [ShiftController::class, 'show'])->name('shift.submit');
Route::get('/shift/submit/{token}/auth',  [ShiftController::class, 'authForm'])->name('shift.auth');
Route::post('/shift/submit/{token}/auth', [ShiftController::class, 'authStore'])->name('shift.auth.store');
Route::post('/shift/submit/{token}/save', [ShiftController::class, 'save'])->name('shift.save');

// ===== 勤怠管理：QRスキャン（認証不要） =====
Route::get('/scan/{token}',  [ScanController::class, 'show'])->name('scan.show');
Route::post('/scan/{token}', [ScanController::class, 'store'])->name('scan.store');

// ===== 管理画面（認証必須） =====
Route::middleware(['auth'])->group(function () {

    // シフト管理
    Route::prefix('shift')->name('shift.')->group(function () {
        Route::get('/',                                    [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('projects', ProjectController::class);
        Route::get('projects/{project}/submissions',       [ProjectController::class, 'submissions'])->name('projects.submissions');
    });

    // 勤怠管理
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',              [AttendanceDashboardController::class, 'index'])->name('dashboard');
        Route::get('/records',       [AttendanceController::class, 'index'])->name('records.index');
        Route::get('/records/export',[AttendanceController::class, 'export'])->name('records.export');
        Route::resource('employees', EmployeeController::class);
        Route::get('employees/{employee}/qr',    [EmployeeController::class, 'qr'])->name('employees.qr');
        Route::post('employees/import',          [EmployeeController::class, 'import'])->name('employees.import');
        Route::resource('workplaces', WorkplaceController::class);
    });
});

// 認証ルート
require __DIR__.'/auth.php';
