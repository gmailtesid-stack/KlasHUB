<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            ['name' => 'ARIYAS PRATAMA RAMADHAN', 'nim' => '231011403268', 'role' => 'ketua_kelas', 'code' => 'KK'],
            ['name' => 'DORRA LADY AFISHE', 'nim' => '231011402314', 'role' => 'bendahara', 'code' => 'BD'],
            ['name' => 'MOCHAMAD FICKRY SATRIA', 'nim' => '231011400980', 'role' => 'sekretaris', 'code' => 'SK']
        ];

        foreach ($admins as $a) {
            \App\Models\Student::updateOrCreate(
                ['nim' => $a['nim']],
                [
                    'name' => $a['name'],
                    'role' => $a['role'],
                    'password' => bcrypt($a['nim'] . $a['code'])
                ]
            );
        }
    }
}
