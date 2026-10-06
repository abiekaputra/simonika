<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinimasaRequest;
use App\Models\Linimasa;
use App\Models\LogAktivitas;
use App\Models\Pegawai;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LinimasaController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::all(); // full list needed for create/edit dropdown
        $proyek = Proyek::with('kategori')->get(); // full list needed for create/edit dropdown
        $linimasa = Linimasa::with(['pegawai', 'proyek'])->paginate(20);
        $linimasaAll = Linimasa::with(['pegawai', 'proyek'])->get(); // needed for vis.js chart

        return view('linimasa.index', compact('pegawai', 'proyek', 'linimasa', 'linimasaAll'));
    }

    public function edit($id)
    {
        $linimasa = Linimasa::with(['pegawai', 'proyek'])->findOrFail($id);

        return response()->json($linimasa);
    }

    public function store(LinimasaRequest $request)
    {
        $project = Proyek::findOrFail($request->integer('proyek_id'));

        DB::transaction(function () use ($request, $project) {
            Linimasa::create($request->validated());
            $this->log('Add Timeline', 'create', "Added timeline entry for project '{$project->nama_proyek}'");
        });

        return redirect()->route('linimasa.index')->with('success', 'Timeline entry added successfully.');
    }

    public function update(LinimasaRequest $request, $id)
    {
        $timeline = Linimasa::findOrFail($id);
        $project = Proyek::findOrFail($request->integer('proyek_id'));

        DB::transaction(function () use ($request, $timeline, $project) {
            $timeline->update($request->validated());
            $this->log('Update Timeline', 'update', "Updated timeline entry for project '{$project->nama_proyek}'");
        });

        return response()->json(['success' => true, 'message' => 'Timeline entry updated successfully.']);
    }

    public function destroy($id)
    {
        $linimasa = Linimasa::with('proyek')->findOrFail($id);
        $proyekNama = $linimasa->proyek->nama_proyek ?? 'unknown';

        DB::transaction(function () use ($linimasa, $proyekNama) {
            $linimasa->delete();
            $this->log('Delete Timeline', 'delete', "Deleted timeline entry for project '{$proyekNama}'");
        });

        return response()->json(['success' => true, 'message' => 'Timeline entry deleted successfully.']);
    }

    private function log(string $activity, string $type, string $detail): void
    {
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'aktivitas' => $activity,
            'tipe_aktivitas' => $type,
            'modul' => 'Linimasa',
            'detail' => $detail,
        ]);
    }
}
