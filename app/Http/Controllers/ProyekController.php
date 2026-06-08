<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proyek;
use App\Models\Kategori;

class ProyekController extends Controller
{
    public function index()
    {
        $proyek = Proyek::with('kategori')->paginate(15);
        $kategori = Kategori::all(); // full list needed for dropdown

        return view('proyek.index', compact('proyek', 'kategori'));
    }

    public function create()
    {
        $kategori = Kategori::all();
        return view('proyek.create', compact('kategori'));
    }

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

        return redirect()->route('proyek.index')->with('success', 'Project added successfully.');
    }

    public function edit($id)
    {
        $proyek = Proyek::findOrFail($id);
        $kategori = Kategori::all();

        return view('proyek.edit', compact('proyek', 'kategori'));
    }

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

        return redirect()->route('proyek.index')->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $proyek = Proyek::findOrFail($id);
        $proyek->delete();

        return redirect()->route('proyek.index')->with('success', 'Project deleted successfully.');
    }
}
