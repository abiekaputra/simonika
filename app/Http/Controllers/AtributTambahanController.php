<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AtributTambahanController extends Controller
{
    public function index()
    {
        $atributs = AtributTambahan::with('aplikasis')->get();
        $aplikasis = Aplikasi::all();
        return view('atribut.index', compact('atributs', 'aplikasis'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'nama_atribut' => 'required|string|max:100|unique:atribut_tambahans',
                'tipe_data' => 'required|in:varchar,number,date,text,enum',
                'enum_options' => 'required_if:tipe_data,enum|array'
            ]);

            $enumOptions = null;
            if ($validated['tipe_data'] === 'enum' && !empty($request->enum_options)) {
                $enumOptions = array_filter($request->enum_options, function($value) {
                    return !empty(trim($value));
                });
                $enumOptions = array_values($enumOptions);
            }

            $atribut = AtributTambahan::create([
                'nama_atribut' => $validated['nama_atribut'],
                'tipe_data' => $validated['tipe_data'],
                'enum_options' => $enumOptions ? json_encode($enumOptions) : null
            ]);

            $aplikasis = Aplikasi::all();
            foreach ($aplikasis as $aplikasi) {
                $aplikasi->atributTambahans()->attach($atribut->id_atribut, [
                    'nilai_atribut' => $request->input('nilai_default') ?? null
                ]);
            }

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Add Attribute',
                'tipe_aktivitas' => 'create',
                'modul' => 'Atribut',
                'detail' => sprintf(
                    "Admin %s added attribute '%s' to %d applications",
                    Auth::user()->nama,
                    $validated['nama_atribut'],
                    count($aplikasis)
                )
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Attribute added successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add attribute: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $aplikasi = Aplikasi::findOrFail($id);
            $atributUpdates = $request->input('atribut', []);
            $successCount = 0;
            $errors = [];

            foreach ($atributUpdates as $atributId => $nilai) {
                try {
                    $atribut = AtributTambahan::findOrFail($atributId);

                    $aplikasi->atributTambahans()->updateExistingPivot($atributId, [
                        'nilai_atribut' => $nilai
                    ]);

                    LogAktivitas::create([
                        'user_id' => Auth::id(),
                        'aktivitas' => 'Update Attribute Value',
                        'tipe_aktivitas' => 'update',
                        'modul' => 'Atribut',
                        'detail' => sprintf(
                            "Updated attribute value '%s' for application '%s'",
                            $atribut->nama_atribut,
                            $aplikasi->nama
                        )
                    ]);

                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update attribute {$atribut->nama_atribut}: " . $e->getMessage();
                }
            }

            if ($successCount > 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully updated ' . $successCount . ' attributes.'
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => implode("\n", $errors)
                ], 422);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating attributes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $atribut = AtributTambahan::with('aplikasis')->findOrFail($id);
            $namaAtribut = $atribut->nama_atribut;
            $jumlahAplikasi = $atribut->aplikasis->count();

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Delete Attribute',
                'tipe_aktivitas' => 'delete',
                'modul' => 'Atribut',
                'detail' => sprintf(
                    "Admin %s deleted attribute '%s' from %d applications",
                    Auth::user()->nama,
                    $namaAtribut,
                    $jumlahAplikasi
                )
            ]);

            $atribut->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Attribute deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to delete attribute.');
        }
    }

    public function detail($id)
    {
        try {
            $atribut = AtributTambahan::with(['aplikasis' => function ($query) {
                $query->orderBy('nama');
            }])->findOrFail($id);

            return response()->json([
                'success' => true,
                'aplikasis' => $atribut->aplikasis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attribute detail.'
            ], 500);
        }
    }

    public function updateNilai(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $atribut = AtributTambahan::findOrFail($id);
            $aplikasi = Aplikasi::findOrFail($request->id_aplikasi);

            $rules = $this->getValidationRules($atribut->tipe_data);
            $validator = Validator::make(
                ['nilai_atribut' => $request->nilai_atribut],
                ['nilai_atribut' => $rules]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Value does not match data type ' . $this->getTypeLabel($atribut->tipe_data)
                ], 422);
            }

            $oldValue = $aplikasi->getNilaiAtribut($id);

            $aplikasi->atributTambahans()->updateExistingPivot($id, [
                'nilai_atribut' => $request->nilai_atribut
            ]);

            LogAktivitas::create([
                'user_id' => Auth::id(),
                'aktivitas' => 'Update Attribute Value',
                'tipe_aktivitas' => 'update',
                'modul' => 'Atribut',
                'detail' => "Updated attribute '{$atribut->nama_atribut}' for application '{$aplikasi->nama}' from '{$oldValue}' to '{$request->nilai_atribut}'"
            ]);

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attribute value.'
            ], 500);
        }
    }

    public function updateAtributValues(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $aplikasi = Aplikasi::findOrFail($id);
            $atributUpdates = $request->input('atribut', []);

            foreach ($atributUpdates as $atributId => $nilai) {
                $atribut = AtributTambahan::findOrFail($atributId);

                $rules = $this->getValidationRules($atribut->tipe_data, $atribut->enum_options);
                $validator = Validator::make(
                    ['nilai' => $nilai],
                    ['nilai' => $rules]
                );

                if ($validator->fails()) {
                    throw new \Exception("Invalid value for attribute {$atribut->nama_atribut}");
                }

                $aplikasi->atributTambahans()->updateExistingPivot($atributId, [
                    'nilai_atribut' => $nilai
                ]);

                LogAktivitas::create([
                    'user_id' => Auth::id(),
                    'aktivitas' => 'Update Attribute Value',
                    'tipe_aktivitas' => 'update',
                    'modul' => 'Atribut',
                    'detail' => sprintf(
                        "Updated attribute value '%s' for application '%s'",
                        $atribut->nama_atribut,
                        $aplikasi->nama
                    )
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Attributes updated successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getValidationRules($tipeData, $enumOptions = null)
    {
        $rules = 'nullable|';

        switch ($tipeData) {
            case 'number':
                $rules .= 'numeric';
                break;
            case 'date':
                $rules .= 'date';
                break;
            case 'text':
                $rules .= 'string';
                break;
            case 'enum':
                if ($enumOptions) {
                    $options = is_string($enumOptions) ? json_decode($enumOptions, true) : $enumOptions;
                    $rules .= 'in:' . implode(',', $options);
                }
                break;
            default:
                $rules .= 'string|max:255';
        }

        return $rules;
    }

    private function getTypeLabel($tipeData)
    {
        switch ($tipeData) {
            case 'number':
                return 'Number';
            case 'date':
                return 'Date';
            case 'varchar':
                return 'Text (max 255 chars)';
            case 'text':
                return 'Long Text';
            case 'enum':
                return 'Options (Enum)';
            default:
                return 'Text';
        }
    }
}
