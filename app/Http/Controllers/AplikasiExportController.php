<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Support\Csv;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AplikasiExportController extends Controller
{
    public function __invoke(): StreamedResponse
    {
        $attributes = AtributTambahan::query()->orderBy('id_atribut')->get();
        $headings = [
            'Nama', 'OPD', 'Uraian', 'Tahun Pembuatan', 'Jenis', 'Basis Aplikasi',
            'Bahasa/Framework', 'Database', 'Pengembang', 'Lokasi Server', 'Status Pemakaian',
            ...$attributes->pluck('nama_atribut')->all(),
        ];

        return Csv::download('aplikasi.csv', $headings, function () use ($attributes) {
            foreach (Aplikasi::with('atributTambahans')->lazyById(200, 'id_aplikasi') as $application) {
                $values = $application->atributTambahans->pluck('pivot.nilai_atribut', 'id_atribut');

                yield [
                    $application->nama, $application->opd, $application->uraian,
                    $application->tahun_pembuatan, $application->jenis, $application->basis_aplikasi,
                    $application->bahasa_framework, $application->database, $application->pengembang,
                    $application->lokasi_server, $application->status_pemakaian,
                    ...$attributes->map(fn ($attribute) => $values->get($attribute->id_atribut, '-'))->all(),
                ];
            }
        });
    }
}
