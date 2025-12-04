<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveApplicationController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/employees/{employee}', [EmployeeController::class, 'show']);

    // Attendance
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::get('/attendances/today', [AttendanceController::class, 'today']);
    Route::post('/attendances/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendances/check-out', [AttendanceController::class, 'checkOut']);

    // Leave Applications
    Route::get('/leaves', [LeaveApplicationController::class, 'index']);
    Route::get('/leaves/{leave}', [LeaveApplicationController::class, 'show']); // NEW
    Route::post('/leaves', [LeaveApplicationController::class, 'store']);
    Route::put('/leaves/{leave}', [LeaveApplicationController::class, 'update']); // NEW
    Route::delete('/leaves/{leave}', [LeaveApplicationController::class, 'destroy']); // NEW
    Route::post('/leaves/{leave}/cancel', [LeaveApplicationController::class, 'cancel']); // NEW
    Route::get('/leave-types', [LeaveApplicationController::class, 'leaveTypes']);
    Route::get('/leave-balance', [LeaveApplicationController::class, 'balance']);
    
    // Get approved leaves for calendar (all employees)
    Route::get('/leaves/calendar/approved', [LeaveApplicationController::class, 'approvedCalendar']); // NEW
});