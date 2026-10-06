<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAtributRequest;
use App\Http\Requests\UpdateAtributRequest;
use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Services\AtributService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AtributController extends Controller
{
    public function __construct(private readonly AtributService $service) {}

    public function index(): View
    {
        return view('atribut.index', [
            'atributs' => AtributTambahan::query()->with('aplikasis')->orderBy('nama_atribut')->paginate(20),
            'atributOptions' => AtributTambahan::query()->orderBy('nama_atribut')->get(),
            'aplikasis' => Aplikasi::query()->with('atributTambahans')->orderBy('nama')->get(),
        ]);
    }

    public function store(StoreAtributRequest $request): JsonResponse
    {
        $attribute = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attribute added successfully.',
            'data' => $attribute,
        ], 201);
    }

    public function edit(AtributTambahan $atribut): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $atribut]);
    }

    public function update(UpdateAtributRequest $request, AtributTambahan $atribut): JsonResponse
    {
        $attribute = $this->service->update($atribut, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attribute updated successfully.',
            'data' => $attribute,
        ]);
    }

    public function destroy(AtributTambahan $atribut): RedirectResponse
    {
        $this->service->delete($atribut);

        return redirect()->route('atribut.index')->with('success', 'Attribute deleted successfully.');
    }

    public function detail(AtributTambahan $atribut): JsonResponse
    {
        return response()->json([
            'success' => true,
            'atribut' => $atribut,
            'aplikasis' => $atribut->aplikasis()->orderBy('nama')->get(),
        ]);
    }
}
