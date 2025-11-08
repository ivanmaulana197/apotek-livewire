<?php
namespace App\Models\Transaksi;

use App\Models\HistoryBarang;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksiBarang extends Model
{
    use HasUlids;

    protected $table    = 'detail_transaksi_barang';
    protected $fillable = [
        'id_history_barang',
        'id_transaksi',
        'harga_beli',
        'komisi',
        'harga',
        'harga_satuan_utama',
        // 'diskon',
        // 'id_promo_barang',
        'total_harga',
    ];
    public $timestamps = false;

    public function detail_barang()
    {
        return $this->belongsTo(HistoryBarang::class, 'id_history_barang');
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    // public function promo_barang()
    // {
    //     return $this->belongsTo(PromoBarang::class, 'id_promo_barang');
    // }
}
