<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\ActivityLogger;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $employee = $user->employee;

        return view('profile.show', compact('user', 'employee'));
    }

    public function edit()
    {
        $user = auth()->user();
        $employee = $user->employee;

        return view('profile.edit', compact('user', 'employee'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // dd($validated);


        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            // Delete old photo if exists
            if ($employee && $employee->photo && Storage::disk('public')->exists($employee->photo)) {
                Storage::disk('public')->delete($employee->photo);
            }

            $validated['photo'] = $request->file('photo')->store('employees', 'public');
        }

        // Update employee only if exists
        if ($employee) {
            $employee->update([
                'phone' => $validated['phone'] ?? $employee->phone,
                'date_of_birth' => $validated['date_of_birth'] ?? $employee->date_of_birth,
                'gender' => $validated['gender'] ?? $employee->gender,
                'address' => $validated['address'] ?? $employee->address,
                'photo' => $validated['photo'] ?? $employee->photo,
            ]);
        }

        ActivityLogger::log('updated', "Updated own profile");

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function password()
    {
        return view('profile.password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLogger::log('updated', "Changed password");

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully.');
    }
}
