<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\Pendataan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OperationalDataTest extends TestCase
{
    use RefreshDatabase;

    private Pengguna $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = Pengguna::create([
            'nama' => 'Admin',
            'email' => 'admin@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_internship_record_requires_exit_after_entry(): void
    {
        $response = $this->actingAs($this->user)->postJson('/pendataan/store', [
            'universitas' => 'Universitas Contoh',
            'jumlah_orang' => 3,
            'tanggal_masuk' => '2026-05-10',
            'tanggal_keluar' => '2026-05-09',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('tanggal_keluar');
        $this->assertDatabaseCount('pendataans', 0);
    }

    public function test_authenticated_user_can_manage_internship_records(): void
    {
        $payload = [
            'universitas' => 'Universitas Contoh',
            'jumlah_orang' => 3,
            'tanggal_masuk' => '2026-05-10',
            'tanggal_keluar' => '2026-06-10',
        ];

        $this->actingAs($this->user)
            ->post('/pendataan/store', $payload)
            ->assertRedirect(route('pendataan.index'));

        $record = Pendataan::firstOrFail();

        $this->actingAs($this->user)
            ->put("/pendataan/update/{$record->id}", [
                ...$payload,
                'jumlah_orang' => 4,
            ])
            ->assertRedirect(route('pendataan.index'));

        $this->assertDatabaseHas('pendataans', [
            'id' => $record->id,
            'jumlah_orang' => 4,
        ]);

        $this->actingAs($this->user)
            ->delete("/pendataan/delete/{$record->id}")
            ->assertRedirect(route('pendataan.index'));

        $this->assertDatabaseCount('pendataans', 0);
    }

    public function test_employee_email_and_phone_are_unique(): void
    {
        Pegawai::create([
            'nama' => 'Dewi',
            'nomor_telepon' => '081234567890',
            'email' => 'dewi@example.test',
        ]);

        $response = $this->actingAs($this->user)->post('/pegawai', [
            'nama' => 'Raka',
            'nomor_telepon' => '081234567890',
            'email' => 'dewi@example.test',
        ]);

        $response->assertSessionHasErrors(['nomor_telepon', 'email']);
        $this->assertDatabaseCount('pegawais', 1);
    }

    public function test_guest_cannot_access_operational_records(): void
    {
        $this->get('/pendataan')->assertRedirect(route('login'));
        $this->get('/pegawai')->assertRedirect(route('login'));
        $this->get('/linimasa')->assertRedirect(route('login'));
    }
}
