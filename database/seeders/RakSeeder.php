<?php
namespace Database\Seeders;

use App\Models\DataMaster\Rak;
use Illuminate\Database\Seeder;

class RakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            "Rak 1",
            "Rak 2",
            "Gudang",
        ];
        foreach ($data as $key => $value) {
            Rak::create(['nama' => $value]);
        }

        // Rak::factory()->count(10000)->create();
    }
}
