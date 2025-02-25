<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Linimasa;
use Carbon\Carbon;

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
            'tenggat_waktu' => 'required|date|after_or_equal:tanggal',
            'status_manual' => 'nullable|string',
        ], [
            'tenggat_waktu.after_or_equal' => 'Tanggal tenggat harus sama atau setelah tanggal pembuatan.',
        ]);

        // Simpan data ke database
        Linimasa::create($request->all());

        return redirect()->route('linimasa.index')->with('success', 'Data linimasa berhasil ditambahkan.');
    }

    /**
     * Memperbarui data linimasa di database.
     */
    public function update(Request $request, $id)
{
    $linimasa = Linimasa::findOrFail($id);

    // Simpan status manual jika ada
    if ($request->has('status_manual')) {
        $linimasa->status_manual = $request->input('status_manual');
    }

    // Simpan perubahan lain
    $linimasa->update($request->except(['status_manual'])); // Abaikan status_manual dalam update massal

    return redirect()->route('linimasa.index')->with('success', 'Proyek berhasil diperbarui!');
}


    /**
     * Menghapus data linimasa dari database.
     */
    public function destroy($id)
    {
        $linimasa = Linimasa::findOrFail($id);
        $linimasa->delete();

        return redirect()->route('linimasa.index')->with('success', 'Data linimasa berhasil dihapus.');
    }

    /**
     * Menampilkan status proyek berdasarkan logika backend tanpa menyimpan status di database.
     */
    public function complete($id)
    {
        $linimasa = Linimasa::findOrFail($id);
        $linimasa->tanggal_selesai = now();
        $linimasa->status_proyek = 'Selesai';
        $linimasa->save();

        return redirect()->route('linimasa.index')->with('success', 'Proyek telah ditandai selesai.');
    }
}
