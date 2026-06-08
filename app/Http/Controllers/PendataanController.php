<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pendataan;

class PendataanController extends Controller
{
    public function index()
    {
        $pendataans = Pendataan::all();
        return view('pendataan.index', compact('pendataans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'universitas' => 'required',
            'jumlah_orang' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required|date|after:tanggal_masuk',
        ]);

        $pendataan = $request->id ? Pendataan::findOrFail($request->id) : new Pendataan();
        $pendataan->fill($request->all());
        $pendataan->save();

        return redirect()->route('pendataan.index')->with('success', $request->id ? 'Record updated successfully.' : 'Record saved successfully.');
    }

    public function edit($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        return response()->json($pendataan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'universitas' => 'required',
            'jumlah_orang' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'tanggal_keluar' => 'required|date|after:tanggal_masuk',
        ]);

        $pendataan = Pendataan::findOrFail($id);
        $pendataan->update([
            'universitas' => $request->universitas,
            'jumlah_orang' => $request->jumlah_orang,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_keluar' => $request->tanggal_keluar,
        ]);

        return redirect()->route('pendataan.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        $pendataan->delete();

        return redirect()->route('pendataan.index')->with('success', 'Record deleted successfully.');
    }
}
