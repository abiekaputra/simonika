<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendataanRequest;
use App\Models\Pendataan;
use App\Services\PendataanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PendataanController extends Controller
{
    public function __construct(private readonly PendataanService $service) {}

    public function index(): View
    {
        $records = Pendataan::query()->orderBy('tanggal_masuk')->get();

        return view('pendataan.index', [
            'pendataans' => Pendataan::query()->latest('tanggal_masuk')->paginate(20),
            'timelineRecords' => $records,
            'timelineData' => $records->map(fn (Pendataan $item) => [
                'id' => $item->id,
                'content' => $item->universitas.' · '.$item->jumlah_orang.' peserta',
                'start' => $item->tanggal_masuk->format('Y-m-d'),
                'end' => $item->tanggal_keluar->copy()->addDay()->format('Y-m-d'),
            ]),
        ]);
    }

    public function store(PendataanRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('pendataan.index')->with('success', 'Data magang berhasil ditambahkan.');
    }

    public function update(PendataanRequest $request, Pendataan $pendataan): RedirectResponse
    {
        $this->service->update($pendataan, $request->validated());

        return redirect()->route('pendataan.index')->with('success', 'Data magang berhasil diperbarui.');
    }

    public function destroy(Pendataan $pendataan): RedirectResponse
    {
        $this->service->delete($pendataan);

        return redirect()->route('pendataan.index')->with('success', 'Data magang berhasil dihapus.');
    }
}
