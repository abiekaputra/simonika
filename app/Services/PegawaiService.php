<?php

namespace App\Services;

use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class PegawaiService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data): Pegawai
    {
        return DB::transaction(function () use ($data) {
            $employee = Pegawai::create($data);
            $this->logger->record('Pegawai', 'Add Employee', 'create', "Added employee '{$employee->nama}'");

            return $employee;
        });
    }

    public function update(Pegawai $employee, array $data): Pegawai
    {
        return DB::transaction(function () use ($employee, $data) {
            $employee->update($data);
            $this->logger->record('Pegawai', 'Update Employee', 'update', "Updated employee '{$employee->nama}'");

            return $employee->refresh();
        });
    }

    public function delete(Pegawai $employee): void
    {
        DB::transaction(function () use ($employee) {
            $name = $employee->nama;
            $employee->delete();
            $this->logger->record('Pegawai', 'Delete Employee', 'delete', "Deleted employee '{$name}'");
        });
    }
}
