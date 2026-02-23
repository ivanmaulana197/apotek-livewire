<?php
namespace Database\Seeders;

use App\Models\Barang\Barang;
use App\Models\Barang\HargaBarang;
use App\Models\Barang\StokBatchBarang;
use App\Models\DataMaster\Kategori;
use App\Models\DataMaster\Rak;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        // Seed beberapa barang random
        Barang::factory()
            ->count(10)
            ->create();

        // Tambah contoh barang manual
        $barang = Barang::create([
            'kode'               => "PRT0",
            'nama'               => "PARACETAMOL",
            'stok'               => 20,
            'stok_min'           => 3,
            'id_satuan_utama'    => Satuan::inRandomOrder()->value('id'),
            'keterangan'         => fake()->sentence(),
            'harga_beli_default' => 2000,
            'mode_jual_utama'    => 'PERSEN',
            'persen_jual_utama'  => 20,
            'harga_jual_utama'   => 2200,
        ]);

        // Relasi kategori & rak
        $barang->kategories()->syncWithoutDetaching(
            Kategori::inRandomOrder()->limit(1)->pluck('id')->toArray()
        );
        $barang->raks()->syncWithoutDetaching(
            Rak::inRandomOrder()->limit(2)->pluck('id')->toArray()
        );

        // Tambah opsi satuan (misal 1 box = 2 strip)
        $satuan_opsi = Satuan::where('id', '!=', $barang->id_satuan_utama)->inRandomOrder()->value('id');
        $barang->opsi_satuan()->attach($satuan_opsi, [
            'rasio_satuan_utama' => 2,
            'rasio_satuan_opsi'  => 1,
        ]);

        // Tambah harga barang
        $satuan_terpakai = $barang->all_satuan;

        foreach ($satuan_terpakai as $index => $satuan) {
            $jumlah_min = fake()->numberBetween(1, 10);
            $mode       = fake()->randomElement(['PERSEN', 'HARGA']);
            if ($mode === 'PERSEN') {
                $persen             = fake()->numberBetween(1, 1000);
                $harga              = ($barang->harga_beli_default * $satuan['rasio']) * (1 + $persen / 100);
                $harga_satuan_utama = $harga / $satuan['rasio'];
            } else {
                $harga              = fake()->numberBetween(($barang->harga_beli_default * $satuan['rasio']), ($barang->harga_beli_default * $satuan['rasio']) * 2);
                $harga_satuan_utama = $harga / $satuan['rasio'];
                $persen             = ($harga - ($barang->harga_beli_default * $satuan['rasio'])) / ($barang->harga_beli_default * $satuan['rasio']) * 100;
            }
            HargaBarang::factory()->create([
                'id_barang'          => $barang->id,
                'id_satuan'          => $satuan['id_satuan'],
                'jumlah_min'         => $jumlah_min,
                'mode'               => $mode,
                'persen'             => $persen,
                'harga'              => $harga,
                'harga_satuan_utama' => $harga_satuan_utama,
            ]);
        }

        // Tambah stok batch
        StokBatchBarang::factory()->create([
            'id_barang' => $barang->id,
        ]);
    }
}