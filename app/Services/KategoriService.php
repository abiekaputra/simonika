<?php

namespace App\Services;

use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KategoriService
{
    public function __construct(private readonly ActivityLogger $logger) {}

    public function create(array $data): Kategori
    {
        return DB::transaction(function () use ($data) {
            $category = Kategori::create($data);
            $this->logger->record('Kategori', 'Add Category', 'create', "Added category '{$category->nama_kategori}'");

            return $category;
        });
    }

    public function update(Kategori $category, array $data): Kategori
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($data);
            $this->logger->record('Kategori', 'Update Category', 'update', "Updated category '{$category->nama_kategori}'");

            return $category->refresh();
        });
    }

    public function delete(Kategori $category): void
    {
        if ($category->proyek()->exists()) {
            throw ValidationException::withMessages([
                'kategori' => 'Kategori masih digunakan oleh proyek dan tidak dapat dihapus.',
            ]);
        }

        DB::transaction(function () use ($category) {
            $name = $category->nama_kategori;
            $category->delete();
            $this->logger->record('Kategori', 'Delete Category', 'delete', "Deleted category '{$name}'");
        });
    }
}
