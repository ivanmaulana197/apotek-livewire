<?php
namespace Database\Factories\Barang;

use App\Models\Barang\Barang;
use App\Models\Barang\HargaBarang;
use App\Models\Barang\StokBatchBarang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang\Barang>
 */
class BarangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $harga_beli_default = $this->faker->numberBetween(100, 200000);
        $mode_jual_utama    = $this->faker->randomElement(['PERSEN', 'HARGA']);
        if ($mode_jual_utama == 'PERSEN') {
            $persen_jual_utama = $this->faker->numberBetween(1, 1000);
            $harga_jual_utama  = $harga_beli_default + ($harga_beli_default * $persen_jual_utama / 100);
        } else {
            $harga_jual_utama  = $this->faker->numberBetween($harga_beli_default, $harga_beli_default*2);
            $persen_jual_utama = ($harga_jual_utama - $harga_beli_default) / $harga_beli_default * 100;
        }

        return [
            'kode'               => $this->faker->unique()->bothify('BRG-####'),
            'nama'               => $this->faker->word(),
            'stok'               => $this->faker->numberBetween(10, 100),
            'stok_min'           => $this->faker->numberBetween(5, 10),
            'stok_real'          => $this->faker->numberBetween(10, 100),
            'id_satuan_utama'    => Satuan::inRandomOrder()->value('id'), //id satuan utama
            'harga_beli_default' => $harga_beli_default,
            'mode_jual_utama'    => $mode_jual_utama,
            'harga_jual_utama'   => $harga_jual_utama,
            'persen_jual_utama'  => $persen_jual_utama,
            'keterangan'         => $this->faker->sentence(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Barang $barang) {
            $id_satuan_utama = $barang->id_satuan_utama;

            // Hubungkan barang dengan rak
            $barang->raks()->syncWithoutDetaching(
                Rak::inRandomOrder()->limit(random_int(1, 3))->pluck('id')->toArray()
            );

            // Hubungkan barang dengan kategori
            $barang->kategories()->syncWithoutDetaching(
                Kategori::inRandomOrder()->limit(random_int(1, 2))->pluck('id')->toArray()
            );

            // Tambah opsi satuan
            $satuan_opsi = Satuan::where('id', '!=', $id_satuan_utama)
                ->inRandomOrder()
                ->take(random_int(0, 2))
                ->get();

            foreach ($satuan_opsi as $satuan) {
                $barang->opsi_satuan()->attach($satuan->id, [
                    'rasio_satuan_utama' => fake()->numberBetween(2, 6),
                    'rasio_satuan_opsi'  => fake()->numberBetween(1, 3),
                ]);
            }

            // Buat stok batch barang
            StokBatchBarang::factory(random_int(1, 3))->create([
                'id_barang' => $barang->id,
            ]);

            // Buat harga barang
            $satuan_terpakai = $barang->all_satuan;

            foreach ($satuan_terpakai as $index => $satuan) {
                $jumlah_min = fake()->numberBetween(1, 10);
                $mode = fake()->randomElement(['PERSEN', 'HARGA']);
                $harga_beli_utama = $barang->harga_beli_default * $satuan['rasio'];
                if ($mode === 'PERSEN') {
                    $persen = fake()->numberBetween(1, 1000);
                    $harga = ($harga_beli_utama) * (1 + $persen / 100);
                    $harga_satuan_utama = $harga / $satuan['rasio'];
                } else {
                    $harga  = fake()->numberBetween($harga_beli_utama, $harga_beli_utama * 2);
                    $persen = ($harga - $harga_beli_utama) / $harga_beli_utama * 100;
                    $harga_satuan_utama = $harga / $satuan['rasio'];
                }
                HargaBarang::factory()->create([
                    'id_barang'  => $barang->id,
                    'id_satuan'  => $satuan['id_satuan'],
                    'jumlah_min' => $jumlah_min,
                    'mode'       => $mode,
                    'persen'     => $persen,
                    'harga'      => $harga,
                    'harga_satuan_utama'=>$harga_satuan_utama
                ]);
            }
        });
    }
}
