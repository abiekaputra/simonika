<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProyekRequest;
use App\Models\Aplikasi;
use App\Models\Kategori;
use App\Models\Proyek;
use App\Services\ProyekService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProyekController extends Controller
{
    public function __construct(private readonly ProyekService $service) {}

    public function index(): View
    {
        return view('proyek.index', [
            'proyek' => Proyek::query()
                ->with(['kategori', 'aplikasi'])
                ->withCount('linimasa')
                ->orderBy('nama_proyek')
                ->paginate(20),
            'kategori' => Kategori::query()->withCount('proyek')->orderBy('nama_kategori')->get(),
            'aplikasi' => Aplikasi::query()->orderBy('nama')->get(['id_aplikasi', 'nama']),
        ]);
    }

    public function store(ProyekRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil ditambahkan.');
    }

    public function update(ProyekRequest $request, Proyek $proyek): RedirectResponse
    {
        $this->service->update($proyek, $request->validated());

        return redirect()->route('proyek.index')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Proyek $proyek): RedirectResponse
    {
        $this->service->delete($proyek);

        return redirect()->route('proyek.index')->with('success', 'Proyek dan linimasa terkait berhasil dihapus.');
    }
}
