<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendataan;

class PendataanController extends Controller
{
    // Menampilkan semua data pendataan
    public function index()
    {
        $pendataans = Pendataan::all();
        return view('pendataan.index', compact('pendataans'));
    }

    // Menyimpan atau memperbarui data pendataan
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'universitas' => 'required',
            'jumlah_orang' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required|date|after:tanggal_masuk',
        ]);

        // Cek apakah ada ID dalam request untuk melakukan update atau create baru
        $pendataan = $request->id ? Pendataan::findOrFail($request->id) : new Pendataan();
        
        // Mengisi data berdasarkan inputan
        $pendataan->fill($request->all());
        
        // Menyimpan data
        $pendataan->save();

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('pendataan.index')->with('success', $request->id ? 'Data berhasil diperbarui!' : 'Data berhasil disimpan!');
    }

    // Mengambil data untuk diedit
    public function edit($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        return response()->json($pendataan); // Mengembalikan data dalam format JSON
    }

    // Memperbarui data pendataan
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'universitas' => 'required',
            'jumlah_orang' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required|date|after:tanggal_masuk',
        ]);

        // Cari pendataan berdasarkan ID
        $pendataan = Pendataan::findOrFail($id);
        
        // Update data berdasarkan inputan
        $pendataan->update([
            'universitas' => $request->universitas,
            'jumlah_orang' => $request->jumlah_orang,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar,
        ]);

        // Kembali ke halaman index dengan pesan sukses
        return redirect()->route('pendataan.index')->with('success', 'Data berhasil diperbarui!');
    }

    // Menghapus data pendataan
    public function destroy($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        $pendataan->delete();
    
        return redirect()->route('pendataan.index')->with('success', 'Data berhasil dihapus!');
    }
}
