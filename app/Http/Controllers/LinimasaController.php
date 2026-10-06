<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinimasaRequest;
use App\Models\Linimasa;
use App\Models\Pegawai;
use App\Models\Proyek;
use App\Services\LinimasaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LinimasaController extends Controller
{
    public function __construct(private readonly LinimasaService $service) {}

    public function index(): View
    {
        $records = Linimasa::query()->with(['pegawai', 'proyek'])->orderBy('mulai')->get();

        return view('linimasa.index', [
            'pegawai' => Pegawai::query()->orderBy('nama')->get(),
            'proyek' => Proyek::query()->with('kategori')->orderBy('nama_proyek')->get(),
            'linimasa' => Linimasa::query()->with(['pegawai', 'proyek'])->latest('mulai')->paginate(20),
            'timelineRecords' => $records,
            'statuses' => Linimasa::STATUSES,
            'timelineData' => $records->map(fn (Linimasa $item) => [
                'id' => $item->id,
                'content' => $item->pegawai->nama.' · '.$item->proyek->nama_proyek,
                'start' => $item->mulai->format('Y-m-d'),
                'end' => $item->tenggat->copy()->addDay()->format('Y-m-d'),
                'title' => $item->status_proyek,
            ]),
        ]);
    }

    public function store(LinimasaRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return to_route('linimasa.index')->with('success', 'Timeline entry added successfully.');
    }

    public function update(LinimasaRequest $request, Linimasa $linimasa): RedirectResponse
    {
        $this->service->update($linimasa, $request->validated());

        return to_route('linimasa.index')->with('success', 'Timeline entry updated successfully.');
    }

    public function destroy(Linimasa $linimasa): RedirectResponse
    {
        $this->service->delete($linimasa);

        return to_route('linimasa.index')->with('success', 'Timeline entry deleted successfully.');
    }
}
