<?php
namespace Database\Factories\Barang;

use App\Models\Barang\Barang;
use App\Models\Barang\HargaBarang;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class HargaBarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = HargaBarang::class;

    public function definition(): array
    {
        // $mode = $this->faker->randomElement(['PERSEN', 'HARGA']);

        return [
            'id_barang' => Barang::factory(),
            'jumlah_min'    => $this->faker->numberBetween(1, 10),
            'id_satuan' => Satuan::factory(),
            // 'mode'      => $mode,
            // 'persen'     => $this->faker->numberBetween(1, 1000)
        ];
    }



}