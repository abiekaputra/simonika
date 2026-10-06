<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Proyek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DomainIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_completed_timeline_requires_consistent_completion_date(): void
    {
        [$admin, $employee, $project] = $this->workflowFixture();
        $base = [
            'pegawai_id' => $employee->id,
            'proyek_id' => $project->id,
            'mulai' => '2026-01-01',
            'tenggat' => '2026-01-31',
        ];

        $this->actingAs($admin)->postJson('/linimasa', [
            ...$base,
            'status_proyek' => 'Tepat Waktu',
        ])->assertJsonValidationErrors('tanggal_selesai');

        $this->actingAs($admin)->postJson('/linimasa', [
            ...$base,
            'status_proyek' => 'Tepat Waktu',
            'tanggal_selesai' => '2026-01-30',
        ])->assertJsonValidationErrors('status_proyek');

        $this->actingAs($admin)->post('/linimasa', [
            ...$base,
            'status_proyek' => 'Selesai Lebih Cepat',
            'tanggal_selesai' => '2026-01-30',
        ])->assertRedirect(route('linimasa.index'));

        $this->assertDatabaseHas('linimasas', ['status_proyek' => 'Selesai Lebih Cepat']);
    }

    public function test_activity_history_survives_admin_deletion(): void
    {
        $superAdmin = $this->user('root@example.test', 'super_admin');
        $admin = $this->user('operator@example.test', 'admin');
        $log = LogAktivitas::create([
            'user_id' => $admin->id_user,
            'aktivitas' => 'Login',
            'tipe_aktivitas' => 'login',
            'modul' => 'Auth',
            'detail' => 'Historical event.',
        ]);

        $this->actingAs($superAdmin)->delete("/admin/{$admin->id_user}")
            ->assertRedirect(route('admin.index'));

        $this->assertDatabaseMissing('penggunas', ['id_user' => $admin->id_user]);
        $this->assertDatabaseHas('log_aktivitas', ['id' => $log->id, 'user_id' => null]);
    }

    public function test_profile_password_requires_current_password_and_records_activity(): void
    {
        $admin = $this->user('operator@example.test', 'admin');

        $this->actingAs($admin)->put('/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'UpdatedPassword2026',
            'password_confirmation' => 'UpdatedPassword2026',
        ])->assertSessionHasErrors('current_password');

        $this->actingAs($admin)->put('/profile/password', [
            'current_password' => 'Password2026',
            'password' => 'UpdatedPassword2026',
            'password_confirmation' => 'UpdatedPassword2026',
        ])->assertSessionHas('success');

        $this->assertTrue(Hash::check('UpdatedPassword2026', $admin->fresh()->password));
        $this->assertDatabaseHas('log_aktivitas', ['aktivitas' => 'Update Password']);
    }

    private function workflowFixture(): array
    {
        $admin = $this->user('admin@example.test', 'admin');
        $category = Kategori::create(['nama_kategori' => 'Internal']);
        $employee = Pegawai::create([
            'nama' => 'Dewi',
            'nomor_telepon' => '081234567890',
            'email' => 'dewi@example.test',
        ]);
        $project = Proyek::create([
            'nama_proyek' => 'Modernisasi Sistem',
            'kategori_id' => $category->id,
            'deskripsi' => 'Pembaruan sistem internal.',
        ]);

        return [$admin, $employee, $project];
    }

    private function user(string $email, string $role): Pengguna
    {
        return Pengguna::create([
            'nama' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('Password2026'),
            'role' => $role,
        ]);
    }
}
