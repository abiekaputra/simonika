<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;

class ProyekController extends Controller
{
    // Menampilkan daftar pegawai
    public function index()
    {
        $proyek = Proyek::all();
        return view('proyek.index', compact('proyek'));
    }

    // Menampilkan form tambah pegawai
    public function create()
    {
        return view('proyek.create');
    }

    // Menyimpan pegawai baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'kategori' => 'required|string|max:15',
            'deskripsi' => 'required|string|max:300', // Perbaikan di sini
        ]);

        Proyek::create($request->all());
        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

    // Menampilkan form edit pegawai
    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);
        return view('proyek.edit', compact('proyek'));
    }

    // Menyimpan perubahan pegawai
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'kategori' => 'required|string|max:15',
            'deskripsi' => 'required|string|max:300', // Perbaikan di sini
        ]);

        // Ambil pegawai berdasarkan ID
        $proyek = Proyek::findOrFail($id);

        // Update data pegawai
        $proyek->update([
            'nama_proyek' => $request->nama_proyek,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
        ]);

        // Redirect dengan pesan sukses
        return redirect()->route('proyek.index')->with('success', 'Data proyek berhasil diperbarui.');
    }

    // Menghapus pegawai
    public function destroy($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->delete();

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil dihapus.');
    }
}