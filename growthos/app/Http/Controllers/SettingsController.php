<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsPasswordRequest;
use App\Http\Requests\UpdateSettingsProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index(): View
    {
        return view('settings.index', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information (name and email).
     */
    public function updateProfile(UpdateSettingsProfileRequest $request): RedirectResponse
    {
        $user      = Auth::user();
        $validated = $request->validated();

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(UpdateSettingsPasswordRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->password = Hash::make($request->validated()['password']);
        $user->save();

        return redirect()
            ->route('settings.index')
            ->with('password_success', 'Password changed successfully.');
    }
}
