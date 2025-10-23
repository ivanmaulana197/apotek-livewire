<?php

namespace Database\Factories\Barang;

use App\Models\Barang\Barang;
use App\Models\Barang\SatuanBarang;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SatuanBarangFactory extends Factory
{

     protected $model = SatuanBarang::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_barang'         => Barang::factory(),
            'id_satuan'         => Satuan::factory(),
            'rasio_satuan_utama'=> $this->faker->numberBetween(1, 10),
            'rasio_satuan_opsi' => $this->faker->numberBetween(1, 10),
        ];
    }
}
