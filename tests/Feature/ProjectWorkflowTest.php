<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Linimasa;
use App\Models\Pegawai;
use App\Models\Pengguna;
use App\Models\Proyek;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProjectWorkflowTest extends TestCase
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

    public function test_timeline_accepts_known_status_and_records_activity(): void
    {
        [$employee, $project] = $this->employeeAndProject();

        $response = $this->actingAs($this->user)->post('/linimasa', [
            'pegawai_id' => $employee->id,
            'proyek_id' => $project->id,
            'status_proyek' => 'Proses',
            'mulai' => '2026-01-01',
            'tenggat' => '2026-01-31',
            'deskripsi' => 'Implementasi tahap pertama.',
        ]);

        $response->assertRedirect(route('linimasa.index'));
        $this->assertDatabaseHas('linimasas', ['proyek_id' => $project->id, 'status_proyek' => 'Proses']);
        $this->assertDatabaseHas('log_aktivitas', ['aktivitas' => 'Add Timeline', 'modul' => 'Linimasa']);
    }

    public function test_timeline_rejects_unknown_status_and_invalid_date_range(): void
    {
        [$employee, $project] = $this->employeeAndProject();

        $response = $this->actingAs($this->user)->postJson('/linimasa', [
            'pegawai_id' => $employee->id,
            'proyek_id' => $project->id,
            'status_proyek' => 'Almost Done',
            'mulai' => '2026-02-01',
            'tenggat' => '2026-01-01',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['status_proyek', 'tenggat']);
        $this->assertDatabaseCount('linimasas', 0);
    }

    public function test_deleting_project_cascades_its_timeline(): void
    {
        [$employee, $project] = $this->employeeAndProject();
        $timeline = Linimasa::create([
            'pegawai_id' => $employee->id,
            'proyek_id' => $project->id,
            'status_proyek' => 'Proses',
            'mulai' => '2026-01-01',
            'tenggat' => '2026-01-31',
        ]);

        $this->actingAs($this->user)->delete("/proyek/{$project->id}")->assertRedirect(route('proyek.index'));

        $this->assertDatabaseMissing('proyeks', ['id' => $project->id]);
        $this->assertDatabaseMissing('linimasas', ['id' => $timeline->id]);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        [, $project] = $this->employeeAndProject();

        $response = $this->actingAs($this->user)->from('/proyek')->delete("/kategori/{$project->kategori_id}");

        $response->assertRedirect('/proyek')->assertSessionHasErrors('kategori');
        $this->assertDatabaseHas('kategori', ['id' => $project->kategori_id]);
    }

    private function employeeAndProject(): array
    {
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

        return [$employee, $project];
    }
}
