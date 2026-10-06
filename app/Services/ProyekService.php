<?php

namespace App\Services;

use App\Models\Proyek;
use Illuminate\Support\Facades\DB;

class ProyekService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data): Proyek
    {
        return DB::transaction(function () use ($data) {
            $project = Proyek::create($data);
            $this->logger->record('Proyek', 'Add Project', 'create', "Added project '{$project->nama_proyek}'");

            return $project;
        });
    }

    public function update(Proyek $project, array $data): Proyek
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update($data);
            $this->logger->record('Proyek', 'Update Project', 'update', "Updated project '{$project->nama_proyek}'");

            return $project->refresh();
        });
    }

    public function delete(Proyek $project): void
    {
        DB::transaction(function () use ($project) {
            $name = $project->nama_proyek;
            $project->delete();
            $this->logger->record('Proyek', 'Delete Project', 'delete', "Deleted project '{$name}'");
        });
    }
}
