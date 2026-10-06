<?php

namespace Database\Seeders;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Models\Kategori;
use App\Models\Linimasa;
use App\Models\Pegawai;
use App\Models\Pendataan;
use App\Models\Pengguna;
use App\Models\Proyek;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedOperator();
        $this->seedApplications();
        $this->seedOperations();
    }

    private function seedOperator(): void
    {
        Pengguna::query()->updateOrCreate(
            ['email' => 'operator@example.test'],
            [
                'nama' => 'Demo Operator',
                'password' => Hash::make(config('simonika.demo_seed.password')),
                'role' => 'admin',
            ]
        );
    }

    private function seedApplications(): void
    {
        $support = AtributTambahan::query()->firstOrCreate(
            ['nama_atribut' => 'Support channel'],
            ['tipe_data' => 'text']
        );
        $criticality = AtributTambahan::query()->firstOrCreate(
            ['nama_atribut' => 'Service criticality'],
            ['tipe_data' => 'enum', 'enum_options' => ['Low', 'Medium', 'High']]
        );

        $applications = [
            ['Portal Layanan', 'Dinas Pelayanan Digital', 'Portal permohonan layanan publik sintetis.', 'Web', 'Website', 'Laravel', 'PostgreSQL', 'Tim Internal', 'Private Cloud', 'Aktif'],
            ['Arsip Terpadu', 'Sekretariat Daerah', 'Katalog arsip dan pencarian dokumen sintetis.', 'Web', 'Website', 'Laravel', 'SQLite', 'Tim Internal', 'On Premise', 'Aktif'],
            ['Pantau Kota', 'Dinas Infrastruktur', 'Dashboard pemantauan indikator operasional sintetis.', 'Dashboard', 'Website', 'JavaScript', 'PostgreSQL', 'Mitra Teknis', 'Private Cloud', 'Tidak Aktif'],
        ];

        foreach ($applications as $index => $data) {
            $application = Aplikasi::query()->updateOrCreate(['nama' => $data[0]], [
                'opd' => $data[1],
                'uraian' => $data[2],
                'tahun_pembuatan' => '2025-01-01',
                'jenis' => $data[3],
                'basis_aplikasi' => $data[4],
                'bahasa_framework' => $data[5],
                'database' => $data[6],
                'pengembang' => $data[7],
                'lokasi_server' => $data[8],
                'status_pemakaian' => $data[9],
            ]);
            $application->atributTambahans()->sync([
                $support->id_atribut => ['nilai_atribut' => 'helpdesk@example.test'],
                $criticality->id_atribut => ['nilai_atribut' => $index === 0 ? 'High' : 'Medium'],
            ]);
        }
    }

    private function seedOperations(): void
    {
        $category = Kategori::query()->firstOrCreate(['nama_kategori' => 'Layanan Digital']);
        $employee = Pegawai::query()->firstOrCreate(
            ['email' => 'naya@example.test'],
            ['nama' => 'Naya Pratama', 'nomor_telepon' => '+628110000001']
        );
        $project = Proyek::query()->updateOrCreate(
            ['nama_proyek' => 'Modernisasi Portal'],
            [
                'kategori_id' => $category->id,
                'aplikasi_id' => Aplikasi::query()->where('nama', 'Portal Layanan')->value('id_aplikasi'),
                'deskripsi' => 'Penyederhanaan alur layanan dan peningkatan aksesibilitas.',
            ]
        );

        Linimasa::query()->updateOrCreate(
            ['pegawai_id' => $employee->id, 'proyek_id' => $project->id],
            [
                'status_proyek' => 'Proses',
                'mulai' => '2026-09-01',
                'tenggat' => '2026-11-30',
                'tanggal_selesai' => null,
                'deskripsi' => 'Validasi katalog layanan dan perbaikan alur pengajuan.',
            ]
        );

        Pendataan::query()->updateOrCreate(
            ['universitas' => 'Universitas Contoh Nusantara', 'tanggal_masuk' => '2026-08-03'],
            ['jumlah_orang' => 4, 'tanggal_keluar' => '2026-11-03']
        );
    }
}
