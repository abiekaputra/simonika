<?php

namespace App\Http\Controllers;

use App\Models\AtributTambahan;
use App\Models\Aplikasi;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AtributController extends Controller
{
    public function index()
    {
        $atributs = AtributTambahan::with('aplikasi')->get();
        $aplikasis = Aplikasi::all();
        return view('atribut.index', compact('atributs', 'aplikasis'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            
            Log::info('Request data:', $request->all());

            $validated = $request->validate([
                'id_aplikasi' => 'required|exists:aplikasis,id_aplikasi',
                'nama_atribut' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('atribut_tambahans')
                        ->where(function ($query) use ($request) {
                            return $query->where('id_aplikasi', $request->id_aplikasi)
                                ->where('nama_atribut', $request->nama_atribut);
                        })
                ],
                'nilai_atribut' => 'nullable|string'
            ]);

            
            $aplikasi = Aplikasi::findOrFail($validated['id_aplikasi']);
            
            
            $atribut = AtributTambahan::create([
                'id_aplikasi' => $validated['id_aplikasi'],
                'nama_atribut' => $validated['nama_atribut'],
                'nilai_atribut' => $validated['nilai_atribut'] ?? null
            ]);

            
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Add Attribute',
                'tipe_aktivitas' => 'create',
                'modul' => 'Atribut',
                'detail' => "Menambahkan atribut '{$atribut->nama_atribut}' to application {$aplikasi->nama}"
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Attribute added successfully.',
                    'data' => $atribut
                ]);
            }

            return redirect()->route('atribut.index')
                ->with('success', 'Attribute added successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error di AtributController@store: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add attribute: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->route('atribut.index')
                ->with('error', 'Failed to add attribute: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $atribut = AtributTambahan::with('aplikasi')->findOrFail($id);
        return response()->json($atribut);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
             untuk debugging
            Log::info('Request data:', $request->all());
            
            $atribut = AtributTambahan::findOrFail($id);
            
            
            $validated = $request->validate([
                'id_aplikasi' => 'required|exists:aplikasis,id_aplikasi',
                'nama_atribut' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('atribut_tambahans', 'nama_atribut')
                        ->where('id_aplikasi', $request->id_aplikasi)
                        ->ignore($id, 'id_atribut')
                ],
                'nilai_atribut' => 'nullable|string|max:255'
            ]);

            
            $atribut->update([
                'id_aplikasi' => $validated['id_aplikasi'],
                'nama_atribut' => $validated['nama_atribut'],
                'nilai_atribut' => $validated['nilai_atribut']
            ]);

            
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Update Attribute',
                'tipe_aktivitas' => 'update',
                'modul' => 'Atribut',
                'detail' => "Mengupdate atribut '{$atribut->nama_atribut}' to application {$atribut->aplikasi->nama}"
            ]);

            DB::commit();
            
            
            
            
            return redirect()->back()->with('success', 'Attribute updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error di AtributController@update: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to update attribute: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $atribut = AtributTambahan::findOrFail($id);
            $namaAtribut = $atribut->nama_atribut;
            $namaAplikasi = $atribut->aplikasi->nama;

            $atribut->delete();

            
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Delete Attribute',
                'tipe_aktivitas' => 'delete',
                'modul' => 'Atribut',
                'detail' => "Menghapus atribut '{$namaAtribut}' from application {$namaAplikasi}"
            ]);

            return redirect()->route('atribut.index')
                ->with('success', 'Attribute deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('atribut.index')
                ->with('error', 'Failed to delete attribute: ' . $e->getMessage());
        }
    }

    public function checkDuplicate(Request $request)
    {
        $exists = AtributTambahan::where('id_aplikasi', $request->id_aplikasi)
            ->where('nama_atribut', $request->nama_atribut)
            ->when($request->current_id, function($query) use ($request) {
                return $query->where('id_atribut', '!=', $request->current_id);
            })
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function detail($id)
    {
        try {
            $atribut = AtributTambahan::with(['aplikasis' => function($query) {
                $query->select('aplikasis.id_aplikasi', 'aplikasis.nama');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'atribut' => $atribut,
                'aplikasis' => $atribut->aplikasis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attribute detail.'
            ], 500);
        }
    }

    public function updateNilai(Request $request, $id_aplikasi)
    {
        try {
            
            $request->validate([
                'id_atribut' => 'required',
                'nilai' => 'nullable'
            ]);

            $aplikasi = Aplikasi::findOrFail($id_aplikasi);
            
            
            $existingValue = DB::table('aplikasi_atribut')
                ->where('id_aplikasi', $id_aplikasi)
                ->where('id_atribut', $request->id_atribut)
                ->value('nilai_atribut');

            
            $newValue = $request->nilai ?: $existingValue;
            
            
            DB::table('aplikasi_atribut')
                ->where('id_aplikasi', $id_aplikasi)
                ->where('id_atribut', $request->id_atribut)
                ->update(['nilai_atribut' => $newValue]);

            return response()->json([
                'success' => true,
                'message' => 'Attribute value updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating atribut nilai: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute value: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeFromApp($id_aplikasi, $id_atribut)
    {
        try {
            $aplikasi = Aplikasi::findOrFail($id_aplikasi);
            $aplikasi->atributTambahans()->detach($id_atribut);

            return response()->json([
                'success' => true,
                'message' => 'Atribut berhasil dihapus from application'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus atribut from application'
            ], 500);
        }
    }
}
