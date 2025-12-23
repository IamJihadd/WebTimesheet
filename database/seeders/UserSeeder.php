<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Manager Test Account
        User::create([
            'name' => 'Manager Test',
            'user_id' => 'DEC001',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'department' => 'Management',
            'level_grade' => 'Project Manager',
            'lokasi_kerja' => 'Jakarta',
            'tanggal_masuk' => now(),
        ]);

        // Karyawan Test Account
        User::create([
            'name' => 'Karyawan Test',
            'user_id' => 'DEC003',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'is_active' => true,
            'department' => 'Information & Technology',
            'level_grade' => 'IT Engineer',
            'lokasi_kerja' => 'Jakarta',
            'tanggal_masuk' => now(),
        ]);
        
        User::create([
            'name' => 'Admin',
            'user_id' => 'DEC002',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'department' => 'Admin',
            'level_grade' => 'Admin',
            'lokasi_kerja' => 'Head Office',
            'tanggal_masuk' => now(),
        ]);

        // Karyawan Non-Aktif (untuk testing)
        User::create([
            'name' => 'Karyawan Keluar',
            'user_id' => 'EMP003',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'is_active' => false,
            'department' => 'Engineering',
            'level_grade' => 'Engineer',
            'lokasi_kerja' => 'Bandung',
            'tanggal_masuk' => now()->subYear(),
            'tanggal_keluar' => now()->subMonth(),
        ]);
    }
}
