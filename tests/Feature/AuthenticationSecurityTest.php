<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_login_failure_does_not_reveal_whether_email_exists(): void
    {
        Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('correct-password'),
            'role' => 'admin',
        ]);

        $knownEmail = $this->post('/login', [
            'email' => 'admin@example.test',
            'password' => 'wrong-password',
        ]);
        $unknownEmail = $this->post('/login', [
            'email' => 'unknown@example.test',
            'password' => 'wrong-password',
        ]);

        $knownEmail->assertSessionHasErrors(['email' => 'Invalid email or password.']);
        $unknownEmail->assertSessionHasErrors(['email' => 'Invalid email or password.']);
    }

    public function test_password_reset_request_does_not_reveal_unknown_email(): void
    {
        Mail::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'unknown@example.test',
        ]);

        $response->assertSessionHas('success', 'If the email is registered, a password reset link has been sent.');
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => 'unknown@example.test']);
        Mail::assertNothingSent();
    }

    public function test_reset_token_is_hashed_and_single_use(): void
    {
        $user = Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);
        $plainToken = 'one-time-reset-token';

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        $response = $this->post('/reset-password', [
            'token' => $plainToken,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_admin_cannot_access_super_admin_routes(): void
    {
        $admin = Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('correct-password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_remember_me_login_persists_a_recaller_token(): void
    {
        $user = Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('correct-password'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'correct-password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertNotNull($user->fresh()->remember_token);
    }
}
