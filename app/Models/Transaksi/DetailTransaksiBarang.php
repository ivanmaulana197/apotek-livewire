<?php
namespace App\Models\Transaksi;

use App\Models\HistoryBarang;
use App\Models\Promo\PromoBarang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class DetailTransaksiBarang extends Model
{
    use HasUlids;

    protected $table    = 'detail_transaksi_barang';
    protected $fillable = [
        'id_history_barang',
        'harga_beli',
        'komisi',
        'harga',
        'harga_satuan_utama',
        'diskon',
        // 'id_promo_barang',
        'total_harga',
    ];

    public function history_barang()
    {
        return $this->belongsTo(HistoryBarang::class, 'id_history_barang');
    }

    public function promo_barang()
    {
        return $this->belongsTo(PromoBarang::class, 'id_promo_barang');
    }
}