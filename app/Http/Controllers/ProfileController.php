<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Pengguna;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function index(): View
    {
        return view('profile.index');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var Pengguna $user */
        $user = $request->user();
        $user->update($request->validated());
        $this->logger->record('Profile', 'Update Profile', 'update', 'Updated account profile.');

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        /** @var Pengguna $user */
        $user = $request->user();
        $user->update(['password' => Hash::make($request->validated('password'))]);
        $this->logger->record('Profile', 'Update Password', 'update', 'Updated account password.');

        return back()->with('success', 'Password updated successfully.');
    }
}
