<?php

namespace App\Services;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use Illuminate\Support\Facades\DB;

class AtributService
{
    public function __construct(
        private readonly AttributeValueValidator $validator,
        private readonly ActivityLogger $logger,
    ) {}

    public function create(array $data): AtributTambahan
    {
        return DB::transaction(function () use ($data) {
            $attribute = AtributTambahan::create($this->normalize($data));
            $applicationIds = Aplikasi::query()->pluck('id_aplikasi');
            $attribute->aplikasis()->sync($applicationIds->mapWithKeys(
                fn ($id) => [$id => ['nilai_atribut' => null]]
            )->all());
            $this->logger->record('Atribut', 'Add Attribute', 'create', "Added global attribute '{$attribute->nama_atribut}'");

            return $attribute;
        });
    }

    public function update(AtributTambahan $attribute, array $data): AtributTambahan
    {
        $normalized = $this->normalize($data);
        $attribute->load('aplikasis');
        $this->validator->validateDefinitionChange($attribute, $normalized);

        return DB::transaction(function () use ($attribute, $normalized) {
            $attribute->update($normalized);
            $this->logger->record('Atribut', 'Update Attribute', 'update', "Updated global attribute '{$attribute->nama_atribut}'");

            return $attribute->refresh();
        });
    }

    public function delete(AtributTambahan $attribute): void
    {
        DB::transaction(function () use ($attribute) {
            $name = $attribute->nama_atribut;
            $attribute->delete();
            $this->logger->record('Atribut', 'Delete Attribute', 'delete', "Deleted global attribute '{$name}'");
        });
    }

    private function normalize(array $data): array
    {
        $data['enum_options'] = $data['tipe_data'] === 'enum'
            ? array_values(array_filter($data['enum_options'] ?? [], fn ($value) => trim($value) !== ''))
            : null;

        return $data;
    }
}
