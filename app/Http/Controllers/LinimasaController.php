<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Linimasa;

class LinimasaController extends Controller
{
    /**
     * Menampilkan semua data linimasa.
     */
    public function index()
    {
        $linimasa = Linimasa::all(); // Ambil semua data dari database
        return view('linimasa.index', compact('linimasa'));
    }

    /**
     * Menampilkan halaman form untuk membuat linimasa baru.
     */
    public function create()
    {
        return view('linimasa.create');
    }

    /**
     * Menyimpan data linimasa baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'nama_proyek' => 'required|string|max:255',
            'nama_pegawai' => 'required|string|max:255',
            'status_proyek' => 'required|string|max:50',
            'tenggat_waktu' => 'required|date|after_or_equal:tanggal',
        ], [
            'tenggat_waktu.after_or_equal' => 'Tanggal tenggat harus sama atau setelah tanggal pembuatan.',
        ]);

        // Simpan data ke database
        Linimasa::create($request->all());

        return redirect()->route('linimasa.index')->with('success', 'Data linimasa berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman form untuk mengedit linimasa yang dipilih.
     */
    public function edit($id)
    {
        $linimasa = Linimasa::findOrFail($id); // Cari data berdasarkan ID

        return view('linimasa.edit', compact('linimasa'));
    }

    /**
     * Memperbarui data linimasa di database.
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tanggal' => 'required|date',
            'nama_proyek' => 'required|string|max:255',
            'nama_pegawai' => 'required|string|max:255',
            'status_proyek' => 'required|string|max:50',
            'tenggat_waktu' => 'required|date|after_or_equal:tanggal',
        ], [
            'tenggat_waktu.after_or_equal' => 'Tanggal tenggat harus sama atau setelah tanggal pembuatan.',
        ]);

        // Cari data berdasarkan ID
        $linimasa = Linimasa::findOrFail($id);

        // Perbarui data
        $linimasa->update($request->all());

        return redirect()->route('linimasa.index')->with('success', 'Data linimasa berhasil diperbarui.');
    }
    public function destroy($id)
{
    // Cari data berdasarkan ID
    $linimasa = Linimasa::findOrFail($id);

    // Hapus data
    $linimasa->delete();

    // Redirect dengan pesan sukses
    return redirect()->route('linimasa.index')->with('success', 'Data linimasa berhasil dihapus.');
}

}
