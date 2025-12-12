<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'Nama_Lengkap' => 'Administrator',
            'Username' => 'admin',
            'nip' => '12345678',
            'jenis_kelamin' => 'L',
            'email' => 'admin@ptm.com',
            'role_name' => 'admin',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
