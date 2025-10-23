<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\DataMaster\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'obat generik',
            'obat tradisional',
            'obat khusus',
            'obat sensitif',
            'obat terapi',
            'susu'
        ];

        foreach($data as $item){
            Kategori::create(["nama"=>$item]);
        }
    }
}
