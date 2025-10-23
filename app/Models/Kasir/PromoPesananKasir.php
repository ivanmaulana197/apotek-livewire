<?php
namespace App\Models\Kasir;

use App\Models\DataMaster\Satuan;
use App\Models\Kasir\PesananKasir;
use App\Models\Promo\PromoBarang;
use App\Models\Promo\PromoTransaksi;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PromoPesananKasir extends Model
{
    use HasUlids;

    protected $table    = 'promo_pesanan_kasir';
    protected $fillable = [
        'id_pesanan_kasir',
        'tipe_promo',
        'id_promo_barang',

        'jumlah',
        'id_satuan',
        'id_promo_transaksi',
        'nominal_promo',
        'deskripsi',
    ];
    // $table->enum('tipe_promo', ['BARANG BONUS','BARANG DISKON', 'TRANSAKSI'])->default('TRANSAKSI');

    public function promo_barang()
    {
        return $this->belongsTo(PromoBarang::class, 'id_promo_barang');
    }

    public function promo_transaksi()
    {
        return $this->belongsTo(PromoTransaksi::class, 'id_promo_transaksi');
    }

    public function pesanan_kasir()
    {
        return $this->belongsTo(PesananKasir::class, 'id_pesanan_kasir');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

}
