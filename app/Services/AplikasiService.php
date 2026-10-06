<?php

namespace App\Services;

use App\Models\Aplikasi;
use App\Models\AtributTambahan;
use Illuminate\Support\Facades\DB;

class AplikasiService
{
    public function __construct(
        private readonly AttributeValueValidator $attributeValidator,
        private readonly ActivityLogger $logger,
    ) {}

    public function create(array $data, array $attributeValues): Aplikasi
    {
        $values = $this->attributeValidator->validate($attributeValues);

        return DB::transaction(function () use ($data, $values) {
            $application = Aplikasi::create($data);
            $this->syncAttributes($application, $values);
            $this->logger->record('Aplikasi', 'Add Application', 'create', "Added application '{$application->nama}'");

            return $application;
        });
    }

    public function update(Aplikasi $application, array $data, array $attributeValues): Aplikasi
    {
        $values = $this->attributeValidator->validate($attributeValues);

        return DB::transaction(function () use ($application, $data, $values) {
            $application->update($data);
            $this->syncAttributes($application, $values);
            $this->logger->record('Aplikasi', 'Update Application', 'update', "Updated application '{$application->nama}'");

            return $application->refresh();
        });
    }

    public function delete(Aplikasi $application): void
    {
        DB::transaction(function () use ($application) {
            $name = $application->nama;
            $application->delete();
            $this->logger->record('Aplikasi', 'Delete Application', 'delete', "Deleted application '{$name}'");
        });
    }

    private function syncAttributes(Aplikasi $application, array $values): void
    {
        $pivotData = AtributTambahan::query()
            ->pluck('id_atribut')
            ->mapWithKeys(fn ($id) => [$id => ['nilai_atribut' => $values[$id] ?? null]])
            ->all();

        $application->atributTambahans()->sync($pivotData);
    }
}
