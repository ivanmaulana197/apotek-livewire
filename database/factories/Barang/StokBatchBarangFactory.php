<?php

namespace Database\Factories\Barang;

use App\Models\Barang\Barang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang\StokBatchBarang>
 */
class StokBatchBarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_barang' => Barang::factory(),
            'jumlah'    => $this->faker->randomFloat(3, 1, 100), // stok decimal
            'tgl_expired'=> $this->faker->dateTimeBetween('now', '+2 years'),
            'harga_beli'=> $this->faker->numberBetween(100, 200000),
        ];
    }
}