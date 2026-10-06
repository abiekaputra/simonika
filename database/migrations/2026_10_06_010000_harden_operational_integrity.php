<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('linimasas', function (Blueprint $table) {
            $table->dropColumn('status_manual');
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->unique('nama');
            $table->unique('nomor_telepon');
        });

        Schema::table('proyeks', function (Blueprint $table) {
            $table->unique('nama_proyek');
        });

        Schema::table('kategori', function (Blueprint $table) {
            $table->unique('nama_kategori');
        });

        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id_user')->on('penggunas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('log_aktivitas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id_user')->on('penggunas')->cascadeOnDelete();
        });

        Schema::table('kategori', fn (Blueprint $table) => $table->dropUnique(['nama_kategori']));
        Schema::table('proyeks', fn (Blueprint $table) => $table->dropUnique(['nama_proyek']));
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropUnique(['nama']);
            $table->dropUnique(['nomor_telepon']);
        });
        Schema::table('linimasas', fn (Blueprint $table) => $table->string('status_manual')->nullable());
    }
};
