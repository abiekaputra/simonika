<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * membuat kolom database yang isi ya kemungnkinan 
     * 1. primary key 
     * 2. kolom tanggal \
     * 3. kolom nama proyek
     * 4. kolom nama pengembang
     * 5. kolom nama proyek yang dikerjakan 
     * 6. kolom status proyek 
     * 7. kolom untuk tenggat waktu 
     */
    public function up(): void
    {
        Schema::create('linimasa', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_proyek');
            $table->string('nama_pegawai');
            $table->string('status_proyek');
            $table->date('tenggat_waktu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linimasa');
    }
};
