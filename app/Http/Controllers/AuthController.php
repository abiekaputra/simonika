<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Pengguna;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Login',
                'tipe_aktivitas' => 'login',
                'modul' => 'Auth',
                'detail' => 'User logged in.',
            ]);

            Auth::user()->update(['last_activity' => now()]);

            if (Auth::user()->isSuperAdmin()) {
                return redirect()->route('super-admin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Logout',
            'tipe_aktivitas' => 'logout',
            'modul' => 'Auth',
            'detail' => 'User logged out.',
        ]);

        Auth::user()->update(['last_activity' => null]);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
        ]);

        $user = Pengguna::where('email', $validated['email'])->first();

        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $validated['email']],
                [
                    'token' => Hash::make($token),
                    'created_at' => now(),
                ]
            );

            Mail::send('emails.reset-password', ['token' => $token], function ($message) use ($validated) {
                $message->to($validated['email']);
                $message->subject('SiMonika — Password Reset');
            });
        }

        return back()->with('success', 'If the email is registered, a password reset link has been sent.');
    }

    public function showResetPasswordForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'password.required' => 'New password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetToken || ! Hash::check($request->token, $resetToken->token)) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }

        if (Carbon::parse($resetToken->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return back()->withErrors(['email' => 'Password reset token has expired.']);
        }

        $user = Pengguna::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Invalid password reset token.']);
        }
        $user->update(['password' => Hash::make($request->password)]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        LogAktivitas::create([
            'user_id' => $user->id_user,
            'aktivitas' => 'Reset Password',
            'tipe_aktivitas' => 'update',
            'modul' => 'auth',
            'detail' => 'User reset their password.',
        ]);

        return redirect()->route('login')->with('success', 'Password reset successfully. Please log in with your new password.');
    }
}
