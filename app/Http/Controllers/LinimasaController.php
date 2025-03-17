<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Linimasa;
use App\Models\Pegawai;
use App\Models\Proyek;

class LinimasaController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::all();
        $proyek = Proyek::all();
        $linimasa = Linimasa::with(['pegawai', 'proyek'])->get();

        return view('linimasa.index', compact('pegawai', 'proyek', 'linimasa'));
    }

    public function create()
    {
        $pegawai = Pegawai::all();
        $proyek = Proyek::all();
        return view('linimasa.create', compact('pegawai', 'proyek'));
    }

    public function edit($id)
    {
        $linimasa = Linimasa::with(['pegawai', 'proyek'])->findOrFail($id);
        return response()->json($linimasa);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'proyek_id' => 'required|exists:proyeks,id',
            'status_proyek' => 'required',
            'mulai' => 'required|date',
            'tenggat' => 'required|date|after_or_equal:mulai',
            'deskripsi' => 'nullable|string',
        ]);

        Linimasa::create([
            'pegawai_id' => $request->pegawai_id,
            'proyek_id' => $request->proyek_id,
            'status_proyek' => $request->status_proyek,
            'mulai' => $request->mulai,
            'tenggat' => $request->tenggat,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('linimasa.index')->with('success', 'Linimasa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'pegawai_id' => 'required|exists:pegawais,id',
            'proyek_id' => 'required|exists:proyeks,id',
            'status_proyek' => 'required',
            'mulai' => 'required|date',
            'tenggat' => 'required|date|after_or_equal:mulai',
            'deskripsi' => 'nullable|string',
        ]);

        $linimasa = Linimasa::find($id);

        if (!$linimasa) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $linimasa->update([
            'pegawai_id' => $request->pegawai_id,
            'proyek_id' => $request->proyek_id,
            'status_proyek' => $request->status_proyek,
            'mulai' => $request->mulai,
            'tenggat' => $request->tenggat,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json(['success' => true, 'message' => 'Data berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        $linimasa = Linimasa::findOrFail($id);
        $linimasa->delete();

        return response()->json(['success' => true, 'message' => 'Data Linimasa berhasil dihapus.']);
    }
}