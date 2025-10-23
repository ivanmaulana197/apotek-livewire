<?php
namespace App\Helpers;

use App\Models\Setting;

class PembulatanHelper
{
    protected static $pembulatan_harga_jual_barang = 50;
    protected static $pembulatan_resep             = 1000;
    protected static $pembulatan_total             = 500;

    public static function init()
    {
        $setting                            = Setting::select('pembulatan_harga_barang', 'pembulatan_resep', 'pembulatan_total')->first();
        self::$pembulatan_harga_jual_barang = $setting->pembulatan_harga_barang;
        self::$pembulatan_resep             = $setting->pembulatan_resep;
        self::$pembulatan_total             = $setting->pembulatan_total;
    }

    public static function pembulatanHargaJualBarang($harga)
    {
        return ceil($harga / self::$pembulatan_harga_jual_barang) * self::$pembulatan_harga_jual_barang;
    }

    public static function hitungPersen($hargaBeli, $hargaJual)
    {
        if ($hargaBeli <= 0) {
            return 0;
        }
        return (($hargaJual - $hargaBeli) / $hargaBeli) * 100;
    }

    public static function pembulatanHargaBarang(array $barang)
    {
        $all_satuan = collect($barang['all_satuan']);
        $hargaBeliAcuan  = $barang['harga_beli_default'];
        $hargaJualUtama  = self::pembulatanHargaJualBarang($barang['harga_jual_utama']);
        $persenJualUtama = self::hitungPersen($hargaBeliAcuan, $hargaJualUtama);

        $barang['harga_jual_utama']  = $hargaJualUtama;
        $barang['persen_jual_utama'] = round($persenJualUtama, 2);

        // === Opsi harga ===
        if (isset($barang['opsi_harga']) && is_array($barang['opsi_harga'])) {
            foreach ($barang['opsi_harga'] as $key => $opsi) {
                // $opsi['harga']              = self::pembulatanHargaJualBarang($opsi['harga']);
                $opsi['harga_satuan_utama'] = self::pembulatanHargaJualBarang($opsi['harga_satuan_utama']);
                $opsi['persen']             = round(self::hitungPersen($hargaBeliAcuan, $opsi['harga_satuan_utama']), 2);
                $opsi['harga'] = $opsi['harga_satuan_utama'] * $all_satuan->firstWhere('id_satuan', $opsi['id_satuan'])['rasio'];
                $barang['opsi_harga'][$key] = $opsi;
            }
        }
        return $barang;
    }

    public static function pembulatanTotalHarga($number, $mode = 'UMUM', $format = true)
    {
        // dd($number, $mode, $format);
        if ($mode == 'UMUM' || $mode == 'MEMBERSHIP') {
            $pembulatan = self::$pembulatan_total;
        } else if ($mode == 'RESEP') {
            $pembulatan = self::$pembulatan_resep;
        }

        $temp = ceil($number / $pembulatan) * $pembulatan;
        return $format ? FormatHelper::formatNumberToString($temp) : $temp;
    }
}
