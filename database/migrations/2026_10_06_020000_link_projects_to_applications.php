<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->unsignedBigInteger('aplikasi_id')->nullable()->after('kategori_id');
            $table->foreign('aplikasi_id')
                ->references('id_aplikasi')
                ->on('aplikasis')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->dropForeign(['aplikasi_id']);
            $table->dropColumn('aplikasi_id');
        });
    }
};
