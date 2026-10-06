<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_mail_failure_rolls_back_new_admin_account(): void
    {
        $superAdmin = Pengguna::create([
            'nama' => 'Super Admin',
            'email' => 'root@example.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);
        Mail::shouldReceive('to')->once()->andThrow(new RuntimeException('SMTP unavailable'));

        $response = $this->actingAs($superAdmin)->postJson('/admin', [
            'nama' => 'Admin Baru',
            'email' => 'new-admin@example.test',
        ]);

        $response->assertInternalServerError()
            ->assertJsonPath('message', 'Unable to create the admin account.');
        $this->assertDatabaseMissing('penggunas', ['email' => 'new-admin@example.test']);
    }

    public function test_admin_cannot_create_another_admin(): void
    {
        $admin = Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)->postJson('/admin', [
            'nama' => 'Admin Baru',
            'email' => 'new-admin@example.test',
        ])->assertForbidden();

        $this->assertDatabaseMissing('penggunas', ['email' => 'new-admin@example.test']);
    }
}
