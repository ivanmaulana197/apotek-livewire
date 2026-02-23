<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'nama'                    => 'Apotek Rajawali',
            'alamat'                  => 'Jl. Rajawali, Bledanah, Karang Dalem, Sampang',
            'kabupaten'               => 'Kabupaten Sampang',
            'email'                   => 'info@apotekrajawali.com',
            'no_telp'                 => '081234567890',
            'pembulatan_harga_barang' => 50,
            'pembulatan_resep'        => 1000,
            'pembulatan_total'        => 500,
            'persentase_jual'         => 10,
            'persentase_jual_resep'   => 10,
            'footer_struk'            => '<b>Apotek Satelit</b><br>Jl. Rajawali, Bledanah, Karang Dalem, Sampang',
        ]);
    }
}