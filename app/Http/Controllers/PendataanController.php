<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendataanRequest;
use App\Models\Pendataan;

class PendataanController extends Controller
{
    public function index()
    {
        $pendataans = Pendataan::paginate(20);

        return view('pendataan.index', compact('pendataans'));
    }

    public function store(PendataanRequest $request)
    {
        Pendataan::create($request->validated());

        return redirect()->route('pendataan.index')->with('success', 'Record saved successfully.');
    }

    public function edit($id)
    {
        $pendataan = Pendataan::findOrFail($id);

        return response()->json($pendataan);
    }

    public function update(PendataanRequest $request, $id)
    {
        $pendataan = Pendataan::findOrFail($id);
        $pendataan->update($request->validated());

        return redirect()->route('pendataan.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        $pendataan = Pendataan::findOrFail($id);
        $pendataan->delete();

        return redirect()->route('pendataan.index')->with('success', 'Record deleted successfully.');
    }
}
