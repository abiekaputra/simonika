<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;

class PegawaiController extends Controller
{
    // Menampilkan daftar pegawai
    public function index()
    {
        $pegawai = Pegawai::all();
        return view('pegawai.index', compact('pegawai'));
    }

    // Menampilkan form tambah pegawai
    public function create()
    {
        return view('pegawai.create');
    }

    // Menyimpan pegawai baru
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:255|unique:pegawais,nama',
                'nomor_telepon' => 'required|string|max:15|unique:pegawais,nomor_telepon',
                'email' => 'required|email|unique:pegawais,email',
            ]);

            Pegawai::create($request->all());

            return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

    // Menampilkan form edit pegawai
    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    // Menyimpan perubahan pegawai
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama' => "required|string|max:255|unique:pegawais,nama,$id",
                'nomor_telepon' => "required|string|max:20|unique:pegawais,nomor_telepon,$id",
                'email' => "required|email|max:255|unique:pegawais,email,$id",
            ]);

            $pegawai = Pegawai::findOrFail($id);
            $pegawai->update($request->all());

            return redirect()->route('pegawai.index')->with('success', 'Data pegawai berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

    // Menghapus pegawai
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus.');
    }
}
