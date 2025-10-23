<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataMaster\MetodePembayaran;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MetodePembayaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = ['TUNAI','TRANSFER'];
        foreach ($data as  $item) {
            # code...
            MetodePembayaran::create([
                'nama' => $item
            ]);
        }
    }
}
