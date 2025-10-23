<?php
namespace App\Models\Transaksi;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use App\Models\Promo\PromoBarang;
use App\Models\Promo\PromoTransaksi;
use App\Models\Transaksi\Transaksi;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class TransaksiListPromo extends Model
{
    use HasUlids;

    protected $table    = 'transaksi_list_promo';
    protected $fillable = [
        'id_transaksi',
        'tipe_promo',
        'id_promo_barang',
        'id_barang', //id barang yang dapat promo
        'id_barang_bonus',
        'jumlah',
        'tgl_expired',
        'id_satuan',
        'id_promo_transaksi',
        'nominal_promo',
        'deskripsi',
    ];
    // $table->enum('tipe_promo', ['BARANG BONUS','BARANG DISKON', 'TRANSAKSI'])->default('TRANSAKSI');

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function promo_barang()
    {
        return $this->belongsTo(PromoBarang::class, 'id_promo_barang');
    }

    public function promo_transaksi()
    {
        return $this->belongsTo(PromoTransaksi::class, 'id_promo_transaksi');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function barang_bonus()
    {
        return $this->belongsTo(Barang::class, 'id_barang_bonus');
    }
}
