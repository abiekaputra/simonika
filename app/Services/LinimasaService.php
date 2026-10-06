<?php

namespace App\Services;

use App\Models\Linimasa;
use Illuminate\Support\Facades\DB;

class LinimasaService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data): Linimasa
    {
        return DB::transaction(function () use ($data) {
            $timeline = Linimasa::create($data);
            $name = $timeline->proyek()->value('nama_proyek');
            $this->logger->record('Linimasa', 'Add Timeline', 'create', "Added timeline for '{$name}'");

            return $timeline;
        });
    }

    public function update(Linimasa $timeline, array $data): Linimasa
    {
        return DB::transaction(function () use ($timeline, $data) {
            $timeline->update($data);
            $name = $timeline->proyek()->value('nama_proyek');
            $this->logger->record('Linimasa', 'Update Timeline', 'update', "Updated timeline for '{$name}'");

            return $timeline->refresh();
        });
    }

    public function delete(Linimasa $timeline): void
    {
        DB::transaction(function () use ($timeline) {
            $name = $timeline->proyek()->value('nama_proyek') ?? 'Unknown project';
            $timeline->delete();
            $this->logger->record('Linimasa', 'Delete Timeline', 'delete', "Deleted timeline for '{$name}'");
        });
    }
}
