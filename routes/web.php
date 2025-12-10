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
        // Route::resource('employees', EmployeeController::class);
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Departments (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        // Route::resource('departments', DepartmentController::class);
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
        Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    // Leave Applications
    // Route::resource('leaves', LeaveApplicationController::class)->names('leaves');
    Route::get('/leaves', [LeaveApplicationController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/create', [LeaveApplicationController::class, 'create'])->name('leaves.create');
    Route::post('/leaves', [LeaveApplicationController::class, 'store'])->name('leaves.store');
    Route::get('/leaves/{leave}', [LeaveApplicationController::class, 'show'])->name('leaves.show');
    Route::get('/leaves/{leave}/edit', [LeaveApplicationController::class, 'edit'])->name('leaves.edit');
    Route::put('/leaves/{leave}', [LeaveApplicationController::class, 'update'])->name('leaves.update');
    Route::delete('/leaves/{leave}', [LeaveApplicationController::class, 'destroy'])->name('leaves.destroy');
    Route::post('/leaves/{leave}/approve', [LeaveApplicationController::class, 'approve'])->name('leaves.approve')->middleware('role:manager');
    Route::post('/leaves/{leave}/reject', [LeaveApplicationController::class, 'reject'])->name('leaves.reject')->middleware('role:manager');
    Route::post('/leaves/{leave}/cancel', [LeaveApplicationController::class, 'cancel'])->name('leaves.cancel');

    // Attendance
    // Route::resource('attendances', AttendanceController::class);
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('/attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('/attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::post('/attendances/generate', [AttendanceController::class, 'generate'])->name('attendances.generate')->middleware('role:admin');
    Route::get('/attendances-report', [AttendanceController::class, 'report'])->name('attendances.report');

    // Events
    // Route::resource('events', EventController::class);
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/calendar', [EventController::class, 'calendar'])->name('events.calendar');
});

require __DIR__.'/auth.php';
