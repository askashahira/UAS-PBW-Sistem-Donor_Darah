<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pendonor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PendonorSeeder extends Seeder
{
    public function run(): void
    {
        $bloodTypes = ['A', 'B', 'AB', 'O'];
        $locations = ['Jakarta', 'Bandung', 'Surabaya', 'Banda Aceh', 'Medan'];

        // Membuat 10 data dummy pendonor
        for ($i = 1; $i <= 10; $i++) {
            // Membuat user
            $user = User::create([
                'name' => 'Pendonor ' . $i,
                'email' => 'donor' . $i . '@example.com',
                'password' => Hash::make('password123'), // Default password
                'phone' => '08' . rand(1111111111, 9999999999),
                'blood_type' => $bloodTypes[array_rand($bloodTypes)],
                'location' => $locations[array_rand($locations)],
                'role' => 'pendonor',
            ]);

            // Membuat data pendonor terkait user
            Pendonor::create([
                'user_id' => $user->id,
                'nama' => $user->name,
                'no_telp' => $user->phone,
                'golongan_darah' => $user->blood_type,
                'asal_daerah' => $user->location,
                'riwayat_donor' => rand(1, 5) . 'x donor sejak 202' . rand(0, 4),
                'status' => 'tersedia', // Status bisa disesuaikan
            ]);
        }
    }
}
