<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendataan;

class PendataanController extends Controller
{
    public function index()
    {
        $pendataans = Pendataan::paginate(20);
        return view('pendataan.index', compact('pendataans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'universitas' => 'required|string|max:255',
            'jumlah_orang' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required|date|after:tanggal_masuk',
        ]);

        Pendataan::create($request->only('universitas', 'jumlah_orang', 'tanggal_masuk', 'tanggal_keluar'));

        return redirect()->route('pendataan.index')->with('success', 'Record saved successfully.');
    }

    public function edit($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        return response()->json($pendataan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'universitas' => 'required|string|max:255',
            'jumlah_orang' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required|date|after:tanggal_masuk',
        ]);

        $pendataan = Pendataan::findOrFail($id);
        $pendataan->update($request->only('universitas', 'jumlah_orang', 'tanggal_masuk', 'tanggal_keluar'));

        return redirect()->route('pendataan.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        $pendataan->delete();

        return redirect()->route('pendataan.index')->with('success', 'Record deleted successfully.');
    }
}
