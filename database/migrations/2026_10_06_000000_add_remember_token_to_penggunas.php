<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('penggunas', 'remember_token')) {
            Schema::table('penggunas', function (Blueprint $table) {
                $table->rememberToken();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('penggunas', 'remember_token')) {
            Schema::table('penggunas', function (Blueprint $table) {
                $table->dropRememberToken();
            });
        }
    }
};
