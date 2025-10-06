<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('admin.profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];

            if ($request->filled('password')) {
                $user->password = Hash::make($validatedData['password']);
            }

            $user->save();

            return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.profile.show')->with('error', 'Failed to update profile. Please try again.');
        }
    }
}
