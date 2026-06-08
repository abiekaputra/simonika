<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::paginate(20);
        return view('pegawai.index', compact('pegawai'));
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:pegawais,nama',
            'nomor_telepon' => 'required|string|max:15|unique:pegawais,nomor_telepon',
            'email' => 'required|email|unique:pegawais,email',
        ]);

        Pegawai::create($request->only('nama', 'nomor_telepon', 'email'));

        return redirect()->route('pegawai.index')->with('success', 'Employee added successfully.');
    }

    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => ['required', 'string', 'max:255', Rule::unique('pegawais', 'nama')->ignore($id)],
            'nomor_telepon' => ['required', 'string', 'max:20', Rule::unique('pegawais', 'nomor_telepon')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('pegawais', 'email')->ignore($id)],
        ]);

        $pegawai = Pegawai::findOrFail($id);
        $pegawai->update($request->only('nama', 'nomor_telepon', 'email'));

        return redirect()->route('pegawai.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Employee deleted successfully.');
    }
}
