<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $matkuls = [
            ['name' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'lecturer' => 'ULIYATUNISA S.Kom., M.Kom.'],
            ['name' => 'Kerja Praktek', 'sks' => 2, 'lecturer' => 'SUTRIYONO S.KOM., M.KOM.'],
            ['name' => 'Teknologi Internet of Things', 'sks' => 2, 'lecturer' => 'JULI GUNAWAN S.T., M.Kom.'],
            ['name' => 'Pemrograman II', 'sks' => 3, 'lecturer' => 'DAWAM AGUNG PRIBADI'],
            ['name' => 'Basis Data II', 'sks' => 3, 'lecturer' => 'ACHMAD LUTFI FUADI S.Kom'],
            ['name' => 'Mobile Programming', 'sks' => 3, 'lecturer' => 'TIO ANDRIAN S.T., M.KOM.'],
            ['name' => 'Sistem Pendukung Keputusan', 'sks' => 2, 'lecturer' => 'ACHMAD SEHAN S.Kom'],
            ['name' => 'Teknik Kompilasi', 'sks' => 2, 'lecturer' => 'ANIS MIRZA S.Kom, M.Kom']
        ];

        foreach ($matkuls as $m) {
            \App\Models\MasterSubject::updateOrCreate(
                ['name' => $m['name']],
                [
                    'sks' => $m['sks'],
                    'default_lecturer' => $m['lecturer']
                ]
            );
        }
    }
}
