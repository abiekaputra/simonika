<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AtributManagementTest extends TestCase
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

    public function test_creating_global_attribute_attaches_it_to_existing_applications(): void
    {
        $application = Aplikasi::create($this->validApplicationData());

        $response = $this->actingAs($this->user)->postJson('/atribut', [
            'nama_atribut' => 'Tingkat Akses',
            'tipe_data' => 'enum',
            'enum_options' => ['Internal', 'Publik'],
        ]);

        $response->assertCreated()->assertJsonPath('data.enum_options.0', 'Internal');
        $attribute = AtributTambahan::where('nama_atribut', 'Tingkat Akses')->firstOrFail();
        $this->assertDatabaseHas('aplikasi_atribut', [
            'id_aplikasi' => $application->id_aplikasi,
            'id_atribut' => $attribute->id_atribut,
            'nilai_atribut' => null,
        ]);
    }

    public function test_enum_attribute_requires_at_least_one_distinct_option(): void
    {
        $response = $this->actingAs($this->user)->postJson('/atribut', [
            'nama_atribut' => 'Tingkat Akses',
            'tipe_data' => 'enum',
            'enum_options' => ['Publik', 'Publik'],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('enum_options.1');
        $this->assertDatabaseMissing('atribut_tambahans', ['nama_atribut' => 'Tingkat Akses']);
    }

    public function test_attribute_values_are_validated_by_definition_type(): void
    {
        $application = Aplikasi::create($this->validApplicationData());
        $attribute = AtributTambahan::create(['nama_atribut' => 'Tanggal Audit', 'tipe_data' => 'date']);
        $application->atributTambahans()->attach($attribute, ['nilai_atribut' => null]);

        $invalid = $this->actingAs($this->user)->postJson("/aplikasi/{$application->id_aplikasi}/atribut", [
            'atribut' => [$attribute->id_atribut => '31/02/2026'],
        ]);
        $invalid->assertUnprocessable()->assertJsonValidationErrors("atribut.{$attribute->id_atribut}");

        $valid = $this->actingAs($this->user)->postJson("/aplikasi/{$application->id_aplikasi}/atribut", [
            'atribut' => [$attribute->id_atribut => '2026-02-28'],
        ]);
        $valid->assertOk();
        $this->assertDatabaseHas('aplikasi_atribut', [
            'id_aplikasi' => $application->id_aplikasi,
            'id_atribut' => $attribute->id_atribut,
            'nilai_atribut' => '2026-02-28',
        ]);
    }

    public function test_definition_type_cannot_invalidate_existing_values(): void
    {
        $application = Aplikasi::create($this->validApplicationData());
        $attribute = AtributTambahan::create(['nama_atribut' => 'Owner', 'tipe_data' => 'varchar']);
        $application->atributTambahans()->attach($attribute, ['nilai_atribut' => 'Tim A']);

        $response = $this->actingAs($this->user)->putJson("/atribut/{$attribute->id_atribut}", [
            'nama_atribut' => 'Owner',
            'tipe_data' => 'number',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors('tipe_data');
        $this->assertSame('varchar', $attribute->fresh()->tipe_data);
        $this->assertDatabaseHas('aplikasi_atribut', ['nilai_atribut' => 'Tim A']);
    }

    public function test_deleting_attribute_removes_its_pivot_values(): void
    {
        $application = Aplikasi::create($this->validApplicationData());
        $attribute = AtributTambahan::create(['nama_atribut' => 'Owner', 'tipe_data' => 'varchar']);
        $application->atributTambahans()->attach($attribute, ['nilai_atribut' => 'Tim A']);

        $this->actingAs($this->user)
            ->delete("/atribut/{$attribute->id_atribut}")
            ->assertRedirect(route('atribut.index'));

        $this->assertDatabaseMissing('atribut_tambahans', ['id_atribut' => $attribute->id_atribut]);
        $this->assertDatabaseMissing('aplikasi_atribut', ['id_atribut' => $attribute->id_atribut]);
    }

    private function validApplicationData(): array
    {
        return [
            'nama' => 'Sistem Uji',
            'opd' => 'Diskominfo',
            'uraian' => null,
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
