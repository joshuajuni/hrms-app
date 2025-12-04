<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use App\Helpers\ActivityLogger;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $employee = $user->employee;

        return view('profile.show', compact('user', 'employee'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        $user = auth()->user();
        $employee = $user->employee;

        return view('profile.edit', compact('user', 'employee'));
    }

    /**
     * Update the user's profile information.
     */
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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $photo = $request->file('photo');
            $filename = 'employee_' . time() . '.' . $photo->getClientOriginalExtension();
            
            $image = Image::read($photo);
            $image->scale(width: 400);
            $image->save(storage_path('app/public/employees/' . $filename));
            
            $validated['photo'] = 'employees/' . $filename;
        }

        // Update employee
        $employee->update([
            'phone' => $validated['phone'] ?? $employee->phone,
            'date_of_birth' => $validated['date_of_birth'] ?? $employee->date_of_birth,
            'gender' => $validated['gender'] ?? $employee->gender,
            'address' => $validated['address'] ?? $employee->address,
            'photo' => $validated['photo'] ?? $employee->photo,
        ]);

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
