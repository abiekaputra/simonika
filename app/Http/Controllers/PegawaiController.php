<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai; // Pastikan model Pegawai sudah dibuat

class PegawaiController extends Controller
{
    // Menampilkan halaman index pegawai
    public function index()
    {
        $pegawai = Pegawai::all(); // Ambil semua data pegawai dari database
        return view('pegawai.index', compact('pegawai')); // Pastikan view 'pegawai.index' dibuat
    }

    // Menyimpan data pegawai ke database
    public function store(Request $request)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:15',
            'email' => 'required|email|max:255',
        ]);

        // Simpan data ke database
        Pegawai::create($validated);

        // Kembalikan respons JSON untuk notifikasi berhasil
        return response()->json(['message' => 'Pegawai berhasil ditambahkan']);
    }
    public function destroy($id)
    {
        // Cari pegawai berdasarkan ID
        $pegawai = Pegawai::findOrFail($id);

        // Hapus pegawai dari database
        $pegawai->delete();

        // Redirect atau kembalikan respons JSON
        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
