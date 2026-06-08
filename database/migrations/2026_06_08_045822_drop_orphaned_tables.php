<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Superseded by pegawais, proyeks, linimasas; kategoris was never used
        Schema::dropIfExists('linimasa');
        Schema::dropIfExists('pegawai');
        Schema::dropIfExists('proyek');
        Schema::dropIfExists('kategoris');
    }

    public function down(): void
    {
        // Intentionally not restoring orphaned tables
    }
};
