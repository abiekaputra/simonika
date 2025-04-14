<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id(); // ID primary key
            $table->string('nama'); // Kolom nama
            $table->string('no_telepon'); // Kolom nomor telepon
            $table->string('email')->unique(); // Kolom email, harus unik
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pegawai'); // Hapus tabel jika rollback
    }
};
