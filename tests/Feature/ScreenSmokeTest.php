<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ScreenSmokeTest extends TestCase
{
    use RefreshDatabase;

    private Pengguna $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = Pengguna::create([
            'nama' => 'Super Admin',
            'email' => 'super-admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);
    }

    public function test_authenticated_screens_render_without_server_errors(): void
    {
        $screens = [
            '/dashboard',
            '/aplikasi',
            '/atribut',
            '/linimasa',
            '/pegawai',
            '/proyek',
            '/pendataan',
            '/profile',
            '/admin',
            '/super-admin/dashboard',
        ];

        foreach ($screens as $screen) {
            $response = $this->actingAs($this->superAdmin)->get($screen);

            self::assertSame(
                200,
                $response->getStatusCode(),
                "Screen {$screen} did not render successfully."
            );
        }
    }

    public function test_category_screen_redirects_to_project_management(): void
    {
        $this->actingAs($this->superAdmin)
            ->get('/kategori')
            ->assertRedirect('/proyek');
    }

    public function test_admin_dashboard_redirects_to_shared_dashboard(): void
    {
        $admin = Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertRedirect('/dashboard');
    }
}
