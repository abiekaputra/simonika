<?php

namespace App\Http\Controllers;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use App\Services\ActivityLogger;
use App\Services\AttributeValueValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AplikasiAtributController extends Controller
{
    public function __construct(
        private readonly AttributeValueValidator $validator,
        private readonly ActivityLogger $logger
    ) {}

    public function show(Aplikasi $aplikasi): JsonResponse
    {
        $values = $aplikasi->atributTambahans()->get()->keyBy('id_atribut');
        $attributes = AtributTambahan::query()->orderBy('nama_atribut')->get();

        $attributes->each(function ($attribute) use ($values) {
            $attribute->setRelation('pivot', $values->get($attribute->id_atribut)?->pivot);
        });

        return response()->json([
            'success' => true,
            'atribut_tambahans' => $attributes,
        ]);
    }

    public function update(Request $request, Aplikasi $aplikasi): JsonResponse
    {
        $request->validate(['atribut' => ['required', 'array']]);
        $values = $this->validator->validate($request->input('atribut'));

        DB::transaction(function () use ($aplikasi, $values) {
            $pivotData = collect($values)->mapWithKeys(
                fn ($value, $id) => [$id => ['nilai_atribut' => $value]]
            );
            $aplikasi->atributTambahans()->syncWithoutDetaching($pivotData);
            $this->logger->record(
                'Atribut',
                'Update Attribute Values',
                'update',
                "Updated attribute values for application '{$aplikasi->nama}'"
            );
        });

        return response()->json(['success' => true, 'message' => 'Attribute values updated successfully.']);
    }
}
