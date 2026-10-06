<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $credentials = Validator::make(config('simonika.demo_seed'), [
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(12)],
        ])->validate();

        DB::table('penggunas')->updateOrInsert([
            'email' => $credentials['email'],
        ], [
            'nama' => 'Super Admin',
            'password' => Hash::make($credentials['password']),
            'role' => 'super_admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
