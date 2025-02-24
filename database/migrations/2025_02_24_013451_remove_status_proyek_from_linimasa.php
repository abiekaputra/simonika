<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('linimasa', function (Blueprint $table) {
            $table->dropColumn('status_proyek');
        });
    }

    public function down()
    {
        Schema::table('linimasa', function (Blueprint $table) {
            $table->string('status_proyek')->nullable();
        });
    }
};

