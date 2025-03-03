<?php

namespace App\Http\Controllers;
use App\Models\Proyek;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index()
    {
        $proyek = Proyek::all(); // Mengambil semua data proyek
        return view('proyek.index', compact('proyek')); // Mengarahkan ke view dengan data proyek
        
    }
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
        ]);
        Proyek::create([
            'nama_proyek' => $validated['nama_proyek'],
            'deskripsi' => $validated['deskripsi'],
        ]);
        
        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil ditambahkan.');

}
public function destroy($id)
{
    $proyek = Proyek::findOrFail($id);
    $proyek->delete();

    return redirect()->route('proyek.index')->with('success', 'Proyek berhasil dihapus.');
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_proyek' => 'required|string|max:255',
        'deskripsi' => 'required|string',
    ]);

    $proyek = Proyek::findOrFail($id);
    $proyek->update($request->all());

    return redirect()->route('proyek.index')->with('success', 'Proyek berhasil diperbarui.');
}

}