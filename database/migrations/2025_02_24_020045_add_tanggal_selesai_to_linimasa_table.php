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
        Schema::table('linimasa', function (Blueprint $table) {
            $table->dateTime('tanggal_selesai')->nullable();
        });
    }

    public function down()
    {
        Schema::table('linimasa', function (Blueprint $table) {
            $table->dropColumn('tanggal_selesai');
        });
    }
};
