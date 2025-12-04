<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\LeaveApplicationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::get('/password', [ProfileController::class, 'password'])->name('password');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // Employees (Admin & Manager)
    Route::middleware(['role:admin,manager'])->group(function () {
        Route::resource('employees', EmployeeController::class);
    });

    // Departments (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('departments', DepartmentController::class);
    });

    // Leave Applications
    Route::resource('leaves', LeaveApplicationController::class)->names('leaves');
    Route::post('/leaves/{leave}/approve', [LeaveApplicationController::class, 'approve'])
        ->name('leaves.approve')
        ->middleware('role:manager');
    Route::post('/leaves/{leave}/reject', [LeaveApplicationController::class, 'reject'])
        ->name('leaves.reject')
        ->middleware('role:manager');
    Route::post('/leaves/{leave}/cancel', [LeaveApplicationController::class, 'cancel'])
        ->name('leaves.cancel');

    // Attendance
    Route::resource('attendances', AttendanceController::class);
    Route::post('/attendances/generate', [AttendanceController::class, 'generate'])
        ->name('attendances.generate')
        ->middleware('role:admin');
    Route::get('/attendances-report', [AttendanceController::class, 'report'])
        ->name('attendances.report');

    // Events
    Route::resource('events', EventController::class);
    Route::get('/calendar', [EventController::class, 'calendar'])->name('events.calendar');
});

require __DIR__.'/auth.php';
