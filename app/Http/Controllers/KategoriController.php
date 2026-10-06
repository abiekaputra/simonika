<?php

namespace App\Http\Controllers;

use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use App\Services\KategoriService;
use Illuminate\Http\RedirectResponse;

class KategoriController extends Controller
{
    public function __construct(private readonly KategoriService $service) {}

    public function index(): RedirectResponse
    {
        return redirect()->route('proyek.index');
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('proyek.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(KategoriRequest $request, Kategori $kategori): RedirectResponse
    {
        $this->service->update($kategori, $request->validated());

        return redirect()->route('proyek.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori): RedirectResponse
    {
        $this->service->delete($kategori);

        return redirect()->route('proyek.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
