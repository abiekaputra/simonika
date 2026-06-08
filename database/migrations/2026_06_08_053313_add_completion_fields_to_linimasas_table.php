<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('linimasas', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('tenggat');
            $table->string('status_manual')->nullable()->after('status_proyek');
        });

        Schema::dropIfExists('pegawai_proyek');
    }

    public function down(): void
    {
        Schema::table('linimasas', function (Blueprint $table) {
            $table->dropColumn(['tanggal_selesai', 'status_manual']);
        });

        Schema::create('pegawai_proyek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('proyek_id')->constrained('proyeks')->cascadeOnDelete();
            $table->timestamps();
        });
    }
};
