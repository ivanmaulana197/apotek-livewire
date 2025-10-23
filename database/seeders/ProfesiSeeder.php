<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataMaster\Profesi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProfesiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profesi::factory(count:5)->create();
    }
}
