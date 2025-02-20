<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Linimasa;


class LinimasaController extends Controller
{
    //
    public function index()
    {
        $linimasa = Linimasa::all();
        return view ('linimasa.index', compact('linimasa'));

    }
     public function create()
    {
        return view('linimasa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'nama_proyek' => 'required|string|max:255',
            'nama_pegawai' => 'required|string|max:255',
            'status_proyek' => 'required|string|max:50',
            'tenggat_waktu' => 'required|date',
        ]);

        Linimasa::create($request->all());

        return redirect()->route('linimasa.index')->with('success', 'Data linimasa berhasil ditambahkan.');
    }
}
