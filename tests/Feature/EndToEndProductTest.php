<?php

namespace Tests\Feature;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Models\Kategori;
use App\Models\Linimasa;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Proyek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EndToEndProductTest extends TestCase
{
    use RefreshDatabase;

    private Pengguna $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Pengguna::create([
            'nama' => 'Operator Integrasi',
            'email' => 'operator@example.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_operator_can_complete_the_integrated_operational_flow(): void
    {
        $this->actingAs($this->user)->postJson('/atribut', [
            'nama_atribut' => 'Kritikalitas',
            'tipe_data' => 'enum',
            'enum_options' => ['Rendah', 'Sedang', 'Tinggi'],
        ])->assertCreated();
        $attribute = AtributTambahan::where('nama_atribut', 'Kritikalitas')->firstOrFail();

        $this->postJson('/aplikasi', [
            ...$this->applicationData(),
            'atribut' => [$attribute->id_atribut => 'Tinggi'],
        ])->assertCreated();
        $application = Aplikasi::where('nama', 'Portal Integrasi')->firstOrFail();

        $this->post('/kategori', ['nama_kategori' => 'Layanan Publik'])
            ->assertRedirect(route('proyek.index'));
        $category = Kategori::where('nama_kategori', 'Layanan Publik')->firstOrFail();

        $this->post('/pegawai', [
            'nama' => 'Dewi Anggraini',
            'nomor_telepon' => '+6281234567890',
            'email' => 'dewi@example.test',
        ])->assertRedirect(route('pegawai.index'));
        $employee = Pegawai::where('email', 'dewi@example.test')->firstOrFail();

        $this->post('/proyek', [
            'nama_proyek' => 'Peningkatan Portal',
            'kategori_id' => $category->id,
            'aplikasi_id' => $application->id_aplikasi,
            'deskripsi' => 'Meningkatkan alur pelayanan publik.',
        ])->assertRedirect(route('proyek.index'));
        $project = Proyek::where('nama_proyek', 'Peningkatan Portal')->firstOrFail();

        $this->post('/linimasa', [
            'pegawai_id' => $employee->id,
            'proyek_id' => $project->id,
            'status_proyek' => 'Proses',
            'mulai' => '2026-01-02',
            'tenggat' => '2026-01-31',
            'deskripsi' => 'Implementasi dan verifikasi alur utama.',
        ])->assertRedirect(route('linimasa.index'));
        $timeline = Linimasa::where('proyek_id', $project->id)->firstOrFail();

        $this->put("/linimasa/{$timeline->id}", [
            'pegawai_id' => $employee->id,
            'proyek_id' => $project->id,
            'status_proyek' => 'Selesai Lebih Cepat',
            'mulai' => '2026-01-02',
            'tenggat' => '2026-01-31',
            'tanggal_selesai' => '2026-01-28',
            'deskripsi' => 'Alur utama lulus verifikasi.',
        ])->assertRedirect(route('linimasa.index'));

        $this->post('/pendataan', [
            'universitas' => 'Universitas Contoh Nusantara',
            'jumlah_orang' => 4,
            'tanggal_masuk' => '2026-02-02',
            'tanggal_keluar' => '2026-05-02',
        ])->assertRedirect(route('pendataan.index'));

        $this->put('/profile', ['nama' => 'Operator Utama'])
            ->assertRedirect();

        $this->get('/dashboard')->assertOk()->assertSee('Portal Integrasi');
        $this->get('/proyek')->assertOk()->assertSeeTextInOrder(['Peningkatan Portal', 'Portal Integrasi']);
        $this->get('/linimasa')->assertOk()->assertSee('Selesai Lebih Cepat');
        $this->get('/pendataan')->assertOk()->assertSee('Universitas Contoh Nusantara');

        $this->getJson("/aplikasi/{$application->id_aplikasi}/detail")
            ->assertOk()
            ->assertJsonPath('data.proyeks.0.nama_proyek', 'Peningkatan Portal')
            ->assertJsonPath('data.proyeks.0.linimasa_count', 1);

        $export = $this->get('/aplikasi/export');
        $export->assertOk();
        $this->assertStringContainsString('Peningkatan Portal', $export->streamedContent());

        foreach (['Atribut', 'Aplikasi', 'Kategori', 'Pegawai', 'Proyek', 'Linimasa', 'Pendataan', 'Profile'] as $module) {
            $this->assertDatabaseHas('log_aktivitas', ['modul' => $module]);
        }
    }

    public function test_deleting_application_unlinks_but_preserves_its_project(): void
    {
        $application = Aplikasi::create($this->applicationData());
        $category = Kategori::create(['nama_kategori' => 'Internal']);
        $project = Proyek::create([
            'nama_proyek' => 'Proyek Bertahan',
            'kategori_id' => $category->id,
            'aplikasi_id' => $application->id_aplikasi,
            'deskripsi' => 'Proyek tidak ikut terhapus bersama inventaris aplikasi.',
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/aplikasi/{$application->id_aplikasi}")
            ->assertOk();

        $this->assertDatabaseHas('proyeks', [
            'id' => $project->id,
            'aplikasi_id' => null,
        ]);
    }

    public function test_project_rejects_an_unknown_application_reference(): void
    {
        $category = Kategori::create(['nama_kategori' => 'Internal']);

        $this->actingAs($this->user)->postJson('/proyek', [
            'nama_proyek' => 'Referensi Tidak Valid',
            'kategori_id' => $category->id,
            'aplikasi_id' => 999999,
            'deskripsi' => 'Harus ditolak oleh validasi relasi.',
        ])->assertUnprocessable()->assertJsonValidationErrors('aplikasi_id');

        $this->assertDatabaseCount('proyeks', 0);
    }

    private function applicationData(): array
    {
        return [
            'nama' => 'Portal Integrasi',
            'opd' => 'Dinas Layanan Digital',
            'uraian' => 'Portal sintetis untuk validasi alur end-to-end.',
            'tahun_pembuatan' => '2026-01-01',
            'jenis' => 'Pelayanan',
            'basis_aplikasi' => 'Website',
            'bahasa_framework' => 'Laravel',
            'database' => 'PostgreSQL',
            'pengembang' => 'Tim Internal',
            'lokasi_server' => 'Private Cloud',
            'status_pemakaian' => 'Aktif',
        ];
    }
}
