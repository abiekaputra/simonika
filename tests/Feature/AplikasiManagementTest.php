<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AplikasiManagementTest extends TestCase
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

    public function test_authenticated_user_can_create_an_application_with_typed_attributes(): void
    {
        $score = AtributTambahan::create(['nama_atribut' => 'Skor', 'tipe_data' => 'number']);
        $platform = AtributTambahan::create([
            'nama_atribut' => 'Platform',
            'tipe_data' => 'enum',
            'enum_options' => ['Internal', 'Publik'],
        ]);

        $response = $this->actingAs($this->user)->postJson('/aplikasi', [
            ...$this->validApplicationData(),
            'atribut' => [
                $score->id_atribut => '92.5',
                $platform->id_atribut => 'Internal',
            ],
        ]);

        $response->assertCreated()->assertJsonPath('success', true);
        $application = Aplikasi::where('nama', 'Sistem Uji')->firstOrFail();
        $this->assertDatabaseHas('aplikasi_atribut', [
            'id_aplikasi' => $application->id_aplikasi,
            'id_atribut' => $score->id_atribut,
            'nilai_atribut' => '92.5',
        ]);
        $this->assertDatabaseHas('log_aktivitas', [
            'user_id' => $this->user->id_user,
            'aktivitas' => 'Add Application',
        ]);
    }

    public function test_invalid_attribute_value_rejects_the_whole_create_operation(): void
    {
        $score = AtributTambahan::create(['nama_atribut' => 'Skor', 'tipe_data' => 'number']);

        $response = $this->actingAs($this->user)->postJson('/aplikasi', [
            ...$this->validApplicationData(),
            'atribut' => [$score->id_atribut => 'not-a-number'],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors("atribut.{$score->id_atribut}");
        $this->assertDatabaseMissing('aplikasis', ['nama' => 'Sistem Uji']);
    }

    public function test_update_keeps_all_global_attributes_and_clears_omitted_values(): void
    {
        $first = AtributTambahan::create(['nama_atribut' => 'Owner', 'tipe_data' => 'varchar']);
        $second = AtributTambahan::create(['nama_atribut' => 'Catatan', 'tipe_data' => 'text']);
        $application = Aplikasi::create($this->validApplicationData());
        $application->atributTambahans()->sync([
            $first->id_atribut => ['nilai_atribut' => 'Lama'],
            $second->id_atribut => ['nilai_atribut' => 'Tetap ada'],
        ]);

        $response = $this->actingAs($this->user)->putJson("/aplikasi/{$application->id_aplikasi}", [
            ...$this->validApplicationData(),
            'nama' => 'Sistem Diperbarui',
            'atribut' => [$first->id_atribut => 'Baru'],
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('aplikasi_atribut', [
            'id_aplikasi' => $application->id_aplikasi,
            'id_atribut' => $first->id_atribut,
            'nilai_atribut' => 'Baru',
        ]);
        $this->assertDatabaseHas('aplikasi_atribut', [
            'id_aplikasi' => $application->id_aplikasi,
            'id_atribut' => $second->id_atribut,
            'nilai_atribut' => null,
        ]);
    }

    public function test_guest_cannot_manage_applications(): void
    {
        $this->postJson('/aplikasi', $this->validApplicationData())->assertUnauthorized();
    }

    private function validApplicationData(): array
    {
        return [
            'nama' => 'Sistem Uji',
            'opd' => 'Diskominfo',
            'uraian' => 'Aplikasi untuk pengujian.',
            'tahun_pembuatan' => '2026-01-01',
            'jenis' => 'Pelayanan',
            'basis_aplikasi' => 'Website',
            'bahasa_framework' => 'Laravel',
            'database' => 'PostgreSQL',
            'pengembang' => 'Internal',
            'lokasi_server' => 'Pusat Data',
            'status_pemakaian' => 'Aktif',
        ];
    }
}
