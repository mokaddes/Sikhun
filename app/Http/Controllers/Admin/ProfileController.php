<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /** Admin account information + password form. */
    public function settings(): Response
    {
        return Inertia::render('Admin/Profile/Settings', [
            'admin' => auth('admin')->user(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $admin = auth('admin')->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
        ]);

        $admin->update($data);

        return back()->with('success', 'Profile information updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = auth('admin')->user();

        if (! Hash::check($data['current_password'], $admin->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password is incorrect.',
            ]);
        }

        $admin->update(['password' => $data['password']]);

        return back()->with('success', 'Password updated.');
    }
}
