<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Linimasa;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Proyek;

class LinimasaController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::all();
        $proyek = Proyek::with('kategori')->get();
        $linimasa = Linimasa::with(['pegawai', 'proyek'])->get();

        return view('linimasa.index', compact('pegawai', 'proyek', 'linimasa'));
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

        $linimasa = Linimasa::create([
            'pegawai_id' => $request->pegawai_id,
            'proyek_id' => $request->proyek_id,
            'status_proyek' => $request->status_proyek,
            'mulai' => $request->mulai,
            'tenggat' => $request->tenggat,
            'deskripsi' => $request->deskripsi,
        ]);

        $proyek = Proyek::find($request->proyek_id);
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Add Timeline',
            'tipe_aktivitas' => 'create',
            'modul' => 'Linimasa',
            'detail' => "Added timeline entry for project '{$proyek->nama_proyek}'",
        ]);

        return redirect()->route('linimasa.index')->with('success', 'Timeline entry added successfully.');
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

        $linimasa = Linimasa::findOrFail($id);

        $linimasa->update([
            'pegawai_id' => $request->pegawai_id,
            'proyek_id' => $request->proyek_id,
            'status_proyek' => $request->status_proyek,
            'mulai' => $request->mulai,
            'tenggat' => $request->tenggat,
            'deskripsi' => $request->deskripsi,
        ]);

        $proyek = Proyek::find($request->proyek_id);
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Update Timeline',
            'tipe_aktivitas' => 'update',
            'modul' => 'Linimasa',
            'detail' => "Updated timeline entry for project '{$proyek->nama_proyek}'",
        ]);

        return response()->json(['success' => true, 'message' => 'Timeline entry updated successfully.']);
    }

    public function destroy($id)
    {
        $linimasa = Linimasa::with('proyek')->findOrFail($id);
        $proyekNama = $linimasa->proyek->nama_proyek ?? 'unknown';

        $linimasa->delete();

        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => 'Delete Timeline',
            'tipe_aktivitas' => 'delete',
            'modul' => 'Linimasa',
            'detail' => "Deleted timeline entry for project '{$proyekNama}'",
        ]);

        return response()->json(['success' => true, 'message' => 'Timeline entry deleted successfully.']);
    }
}
