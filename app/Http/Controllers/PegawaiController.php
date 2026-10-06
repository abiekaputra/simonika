<?php

namespace App\Http\Controllers;

use App\Http\Requests\PegawaiRequest;
use App\Models\Pegawai;
use App\Services\PegawaiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function __construct(private readonly PegawaiService $service) {}

    public function index(): View
    {
        return view('pegawai.index', [
            'pegawai' => Pegawai::query()->withCount('linimasa')->orderBy('nama')->paginate(20),
        ]);
    }

    public function store(PegawaiRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function update(PegawaiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $this->service->update($pegawai, $request->validated());

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        $this->service->delete($pegawai);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai dan linimasa terkait berhasil dihapus.');
    }
}
