<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Pegawai;
use App\Models\Proyek;
use App\Models\Linimasa;

class LinimasaController extends Controller
{
    // Display the linimasa index page
    public function index()
    {
        $linimasas = Linimasa::with(['kategori', 'pegawai', 'proyek'])->get();
        $timelineData = $linimasas->map(function ($linimasa) {
            return [
                'id' => $linimasa->id,
                'content' => $linimasa->proyek ? $linimasa->proyek->nama_proyek : 'Tidak Ada Nama Proyek',
                'start' => $linimasa->tanggal_mulai,
                'end' => $linimasa->tanggal_deadline,
            ];
        });
            // Definisikan $kategoris
            $kategoris = Proyek::select('kategori_id')->distinct()->get(); 

    // Pegawais jika diperlukan
    $pegawais = Pegawai::all();
    $proyeks = Proyek::all();

        return view('linimasa.index', compact('linimasas', 'pegawais', 'proyeks'));
    }

    // Store a new linimasa record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'proyek_id' => 'required|exists:proyeks,id', 
            'mulai' => 'required|date',
            'tenggat' => 'required|date|after_or_equal:mulai',
        ]);

        Linimasa::create($validated);

        return redirect()->route('linimasa.index')->with('success', 'Linimasa berhasil ditambahkan.');
    }
}
