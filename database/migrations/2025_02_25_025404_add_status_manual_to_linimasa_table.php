<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('linimasa', function (Blueprint $table) {
            //
            $table->string('status_manual')->nullable()->after('tanggal_selesai');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linimasa', function (Blueprint $table) {
            //
            $table->dropColumn('status_manual');
        });
    }
};
