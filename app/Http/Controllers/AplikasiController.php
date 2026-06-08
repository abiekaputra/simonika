<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AplikasiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\AtributTambahan;
use App\Traits\CatatAktivitas;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AplikasiController extends Controller
{
    use CatatAktivitas;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $aplikasis = Aplikasi::all();
        $atributs = AtributTambahan::all();
        return view('aplikasi.index', compact('aplikasis', 'atributs'));
    }

    public function create()
    {
        $atributs = AtributTambahan::all();
        return view('aplikasi.create', compact('atributs'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|unique:aplikasis,nama',
                'opd' => 'required',
                'uraian' => 'nullable',
                'tahun_pembuatan' => 'required|date',
                'jenis' => 'required',
                'basis_aplikasi' => 'required|in:Website,Desktop,Mobile',
                'bahasa_framework' => 'required',
                'database' => 'required',
                'pengembang' => 'required',
                'lokasi_server' => 'required',
                'status_pemakaian' => 'required|in:Aktif,Tidak Aktif'
            ]);

            DB::beginTransaction();

            $aplikasi = Aplikasi::create($validated);

            if ($request->has('atribut')) {
                foreach ($request->atribut as $id_atribut => $nilai) {
                    if (!empty($nilai)) {
                        $aplikasi->atributTambahans()->attach($id_atribut, ['nilai_atribut' => $nilai]);
                    }
                }
            }

            DB::commit();

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Add Application',
                'tipe_aktivitas' => 'create',
                'modul' => 'Aplikasi',
                'detail' => "Added application '{$aplikasi->nama}'"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application added successfully.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $aplikasi = Aplikasi::with('atributTambahans')->findOrFail($id);

            return response()->json([
                'success' => true,
                'aplikasi' => $aplikasi,
                'atribut_tambahan' => $aplikasi->atributTambahans
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application detail: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $aplikasi = Aplikasi::with(['atributTambahans' => function($query) {
                $query->withPivot('nilai_atribut');
            }])->findOrFail($id);

            $atributs = AtributTambahan::all();

            return response()->json([
                'success' => true,
                'aplikasi' => $aplikasi,
                'atributs' => $atributs
            ]);
        } catch (\Exception $e) {
            Log::error('Error in edit method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $aplikasi = Aplikasi::findOrFail($id);

            $validated = $request->validate([
                'nama' => ['required', Rule::unique('aplikasis')->ignore($aplikasi->id_aplikasi, 'id_aplikasi')],
                'opd' => 'required',
                'uraian' => 'nullable',
                'tahun_pembuatan' => 'required|date',
                'jenis' => 'required',
                'basis_aplikasi' => 'required|in:Website,Desktop,Mobile',
                'bahasa_framework' => 'required',
                'database' => 'required',
                'pengembang' => 'required',
                'lokasi_server' => 'required',
                'status_pemakaian' => 'required|in:Aktif,Tidak Aktif'
            ]);

            $aplikasi->update($validated);

            if ($request->has('atribut')) {
                $aplikasi->atributTambahans()->detach();

                foreach ($request->atribut as $id_atribut => $nilai) {
                    if (!empty($nilai)) {
                        $aplikasi->atributTambahans()->attach($id_atribut, ['nilai_atribut' => $nilai]);
                    }
                }
            }

            DB::commit();

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Update Application',
                'tipe_aktivitas' => 'update',
                'modul' => 'Aplikasi',
                'detail' => "Updated application '{$aplikasi->nama}'"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully.'
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating aplikasi: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $aplikasi = Aplikasi::findOrFail($id);

            $aplikasi->atributTambahans()->detach();
            $aplikasi->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application: ' . $e->getMessage()
            ]);
        }
    }

    public function destroyByNama($nama)
    {
        try {
            $aplikasi = Aplikasi::where('nama', $nama)->firstOrFail();
            $namaAplikasi = $aplikasi->nama;

            $aplikasi->delete();

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Delete Application',
                'tipe_aktivitas' => 'delete',
                'modul' => 'Aplikasi',
                'detail' => "Deleted application '{$namaAplikasi}'"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        try {
            Log::info('Starting export process');

            $count = Aplikasi::count();
            Log::info("Found {$count} records to export");

            $export = new AplikasiExport();
            Log::info('AplikasiExport instance created');

            return Excel::download($export, 'aplikasi.xlsx');
        } catch (\Exception $e) {
            Log::error('Export error details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'An error occurred while exporting data: ' . $e->getMessage());
        }
    }

    public function getChartData()
    {
        $statusData = Aplikasi::select('status_pemakaian', DB::raw('count(*) as total'))
            ->groupBy('status_pemakaian')
            ->get();

        $jenisData = Aplikasi::select('jenis', DB::raw('count(*) as total'))
            ->groupBy('jenis')
            ->get();

        $basisData = Aplikasi::select('basis_aplikasi', DB::raw('count(*) as total'))
            ->groupBy('basis_aplikasi')
            ->get();

        $pengembangData = Aplikasi::select('pengembang', DB::raw('count(*) as total'))
            ->groupBy('pengembang')
            ->get();

        return response()->json([
            'statusData' => $statusData,
            'jenisData' => $jenisData,
            'basisData' => $basisData,
            'pengembangData' => $pengembangData
        ]);
    }

    public function editByNama($nama)
    {
        try {
            $aplikasi = Aplikasi::where('nama', $nama)->firstOrFail();

            $atributs = AtributTambahan::whereHas('aplikasis', function ($query) use ($aplikasi) {
                $query->where('aplikasis.id_aplikasi', $aplikasi->id_aplikasi);
            })->get();

            $existingAtributs = $aplikasi->atributTambahans()
                ->get()
                ->pluck('pivot.nilai_atribut', 'id_atribut')
                ->toArray();

            return view('aplikasi.edit', compact('aplikasi', 'atributs', 'existingAtributs'));
        } catch (\Exception $e) {
            Log::error('Error in editByNama: ' . $e->getMessage());
            return redirect()->route('aplikasi.index')
                ->with('error', 'Failed to load application data.');
        }
    }

    public function updateByNama(Request $request, $nama)
    {
        try {
            $aplikasi = Aplikasi::where('nama', $nama)->firstOrFail();
            $oldStatus = $aplikasi->status_pemakaian;

            $aplikasi->update($request->all());

            LogAktivitas::create([
                'user_id' => Auth::user()->id_user,
                'aktivitas' => 'Update Application',
                'tipe_aktivitas' => 'update',
                'modul' => 'aplikasi',
                'detail' => "Updated application status '{$nama}' from '{$oldStatus}' to '{$request->status_pemakaian}'"
            ]);

            return redirect()->route('aplikasi.index')
                ->with('success', 'Application updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update application: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $aplikasi = Aplikasi::with('atributTambahans')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $aplikasi,
                'message' => 'Application detail loaded.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in detail method: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application detail: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAtribut($id)
    {
        $aplikasi = Aplikasi::with('atributTambahans')->findOrFail($id);
        return response()->json([
            'success' => true,
            'atribut_tambahans' => $aplikasi->atributTambahans
        ]);
    }

    public function updateAtribut(Request $request, $id)
    {
        try {
            $aplikasi = Aplikasi::findOrFail($id);

            foreach ($request->nilai_atribut as $atributId => $nilai) {
                DB::table('aplikasi_atribut')
                    ->where('id_aplikasi', $id)
                    ->where('id_atribut', $atributId)
                    ->update(['nilai_atribut' => $nilai]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attribute values updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute values: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getFieldLabel($field)
    {
        $labels = [
            'nama' => 'Name',
            'opd' => 'OPD',
            'uraian' => 'Description',
            'tahun_pembuatan' => 'Year Built',
            'jenis' => 'Type',
            'basis_aplikasi' => 'Application Base',
            'bahasa_framework' => 'Language/Framework',
            'database' => 'Database',
            'pengembang' => 'Developer',
            'lokasi_server' => 'Server Location',
            'status_pemakaian' => 'Usage Status'
        ];

        return $labels[$field] ?? $field;
    }
}
