<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Helpers\ActivityLogger;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Login and get token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            ActivityLogger::log('failed_login', "Failed login attempt for: {$request->email}");
            
            return $this->unauthorizedResponse('The provided credentials are incorrect.');
        }

        if (!$user->is_active) {
            ActivityLogger::log('inactive_login_attempt', "Inactive user login attempt: {$user->email}");
            
            return $this->forbiddenResponse('Your account is inactive. Please contact administrator.');
        }

        // Delete old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        ActivityLogger::log('api_login', "API login successful via mobile app");

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
            ],
            'employee' => $user->employee ? [
                'id' => $user->employee->id,
                'employee_code' => $user->employee->employee_code,
                'full_name' => $user->employee->full_name,
                'department' => $user->employee->department ? $user->employee->department->name : null,
                'position' => $user->employee->position,
                'photo' => $user->employee->photo ? asset('storage/' . $user->employee->photo) : null,
            ] : null,
        ], 'Login successful');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        ActivityLogger::log('api_logout', "API logout via mobile app");
        
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout successful');
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
            ],
            'employee' => $user->employee ? [
                'id' => $user->employee->id,
                'employee_code' => $user->employee->employee_code,
                'full_name' => $user->employee->full_name,
                'first_name' => $user->employee->first_name,
                'last_name' => $user->employee->last_name,
                'phone' => $user->employee->phone,
                'department' => $user->employee->department ? [
                    'id' => $user->employee->department->id,
                    'name' => $user->employee->department->name,
                ] : null,
                'position' => $user->employee->position,
                'photo' => $user->employee->photo ? asset('storage/' . $user->employee->photo) : null,
                'annual_leave_balance' => $user->employee->annual_leave_balance,
                'sick_leave_balance' => $user->employee->sick_leave_balance,
            ] : null,
        ]);
    }
}
