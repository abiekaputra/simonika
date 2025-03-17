<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('linimasas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('proyek_id')->constrained('proyeks')->onDelete('cascade');
            $table->enum('status_proyek', [
                'Selesai Lebih Cepat', 'Tepat Waktu', 'Terlambat', 'Revisi', 'Proses', 'To Do Next'
            ])->default('Proses');
            $table->date('mulai')->nullable(); // Diubah menjadi nullable
            $table->date('tenggat')->nullable(); // Diubah menjadi nullable
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linimasas', function (Blueprint $table) {
            //
        });
    }
};
