<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SatuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'pcs',
            'tablet',
            'box',
            'botol',
            'kotak',

        ];
        foreach ($data as $item) {
            Satuan::create(['nama'=>$item]);
            # code...
        }
    }
}
