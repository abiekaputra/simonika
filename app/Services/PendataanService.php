<?php

namespace App\Services;

use App\Models\Pendataan;
use Illuminate\Support\Facades\DB;

class PendataanService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data): Pendataan
    {
        return DB::transaction(function () use ($data) {
            $record = Pendataan::create($data);
            $this->logger->record('Pendataan', 'Add Internship', 'create', "Added internship for '{$record->universitas}'");

            return $record;
        });
    }

    public function update(Pendataan $record, array $data): Pendataan
    {
        return DB::transaction(function () use ($record, $data) {
            $record->update($data);
            $this->logger->record('Pendataan', 'Update Internship', 'update', "Updated internship for '{$record->universitas}'");

            return $record->refresh();
        });
    }

    public function delete(Pendataan $record): void
    {
        DB::transaction(function () use ($record) {
            $university = $record->universitas;
            $record->delete();
            $this->logger->record('Pendataan', 'Delete Internship', 'delete', "Deleted internship for '{$university}'");
        });
    }
}
