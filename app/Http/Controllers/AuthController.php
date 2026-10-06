<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->only('email'));
        }

        $request->session()->regenerate();
        $this->logger->record('Auth', 'Login', 'login', 'User logged in.');
        /** @var Pengguna $user */
        $user = Auth::user();
        $user->update(['last_activity' => now()]);

        return redirect()->route($user->isSuperAdmin() ? 'super-admin.dashboard' : 'dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        /** @var Pengguna $user */
        $user = Auth::user();
        $this->logger->record('Auth', 'Logout', 'logout', 'User logged out.');
        $user->update(['last_activity' => null]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $validated = $request->validate(['email' => ['required', 'email']]);
        $user = Pengguna::query()->where('email', $validated['email'])->first();

        if ($user) {
            $this->deliverResetLink($user);
        }

        return back()->with('success', 'If the email is registered, a password reset link has been sent.');
    }

    public function showResetPasswordForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);
        $record = DB::table('password_reset_tokens')->where('email', $validated['email'])->first();

        if (! $record || ! Hash::check($validated['token'], $record->token)) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

            return back()->withErrors(['email' => 'Password reset token has expired.']);
        }

        $user = Pengguna::query()->where('email', $validated['email'])->first();
        if (! $user) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
        $this->logger->record('Auth', 'Reset Password', 'update', 'User reset their password.', $user->id_user);

        return redirect()->route('login')->with('success', 'Password reset successfully. Please log in with your new password.');
    }

    private function deliverResetLink(Pengguna $user): void
    {
        $token = Str::random(64);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );
        Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email)->subject('SiMonika — Password Reset');
        });
    }
}
