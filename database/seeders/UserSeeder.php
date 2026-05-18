<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate table to prevent duplicates
        User::truncate();
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@pos.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        User::create([
            'nama' => 'Kasir Utama',
            'email' => 'kasir@pos.com',
            'password' => Hash::make('kasir123'),
            'role' => 'kasir'
        ]);

        User::create([
            'nama' => 'Staff Gudang',
            'email' => 'gudang@pos.com',
            'password' => Hash::make('gudang123'),
            'role' => 'gudang'
        ]);
    }
}