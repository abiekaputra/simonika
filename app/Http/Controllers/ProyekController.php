<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Kategori; 

class ProyekController extends Controller
{
    // Menampilkan daftar pegawai
    public function index()
    {
        $proyek = Proyek::all();  
        $kategori = Kategori::all();

        return view('proyek.index', compact('proyek', 'kategori'));
    }

    // Menampilkan form tambah pegawai
    public function create()
    {
        $kategori = Kategori::all();
        return view('proyek.create', compact('kategori'));
    }

    // Menyimpan pegawai baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        Proyek::create([
            'nama_proyek' => $request->nama_proyek,
            'deskripsi' => $request->deskripsi,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil ditambahkan!');
    }

    // Menampilkan form edit pegawai
    public function edit($id)
    {
        $proyek = Proyek::find($id); // Ambil proyek berdasarkan ID

        $kategori = Kategori::all(); // Ambil semua kategori

        return view('proyek.edit', compact('proyek', 'kategori'));
    }

    // Menyimpan perubahan pegawai
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'kategori_id' => 'required|integer|exists:kategori,id',
            'deskripsi' => 'required|string',
        ]);

        $proyek = Proyek::findOrFail($id);
        $proyek->update([
            'nama_proyek' => $request->nama_proyek,
            'kategori_id' => (int) $request->kategori_id,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil diperbarui!');
    }

    // Menghapus pegawai
    public function destroy($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->delete();

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil dihapus.');
    }
}