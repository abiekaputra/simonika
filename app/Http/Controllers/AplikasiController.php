<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAplikasiRequest;
use App\Http\Requests\UpdateAplikasiRequest;
use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Services\AplikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AplikasiController extends Controller
{
    public function __construct(private readonly AplikasiService $service) {}

    public function index(): View
    {
        return view('aplikasi.index', [
            'aplikasis' => Aplikasi::query()->withCount('proyeks')->orderBy('nama')->get(),
            'atributs' => AtributTambahan::query()->orderBy('nama_atribut')->get(),
        ]);
    }

    public function store(StoreAplikasiRequest $request): JsonResponse
    {
        $this->service->create($request->safe()->except('atribut'), $request->input('atribut', []));

        return response()->json([
            'success' => true,
            'message' => 'Application added successfully.',
        ], 201);
    }

    public function detail(Aplikasi $aplikasi): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $aplikasi->load([
                'atributTambahans',
                'proyeks' => fn ($query) => $query
                    ->with('kategori')
                    ->withCount('linimasa')
                    ->orderBy('nama_proyek'),
            ]),
            'message' => 'Application detail loaded.',
        ]);
    }

    public function edit(Aplikasi $aplikasi): JsonResponse
    {
        return response()->json([
            'success' => true,
            'aplikasi' => $aplikasi->load('atributTambahans'),
            'atributs' => AtributTambahan::query()->orderBy('nama_atribut')->get(),
        ]);
    }

    public function update(UpdateAplikasiRequest $request, Aplikasi $aplikasi): JsonResponse
    {
        $this->service->update($aplikasi, $request->safe()->except('atribut'), $request->input('atribut', []));

        return response()->json([
            'success' => true,
            'message' => 'Application updated successfully.',
        ]);
    }

    public function destroy(Aplikasi $aplikasi): JsonResponse
    {
        $this->service->delete($aplikasi);

        return response()->json([
            'success' => true,
            'message' => 'Application deleted successfully.',
        ]);
    }
}
