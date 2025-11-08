<?php
namespace App\Models\Faktur;

use App\Models\HistoryBarang;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailFakturBarang extends Model
{
    use HasUlids;
    protected $table    = 'detail_faktur_barang';
    protected $fillable = [
        'id_history_barang',
        // 'id_stok_batch_barang',
        'id_faktur',
        'harga_beli',
    ];
    public $timestamps = false;

    public function detail_barang(): BelongsTo// ambil dari Table History Barang
    {
        return $this->belongsTo(HistoryBarang::class, 'id_history_barang');
    }

    public function faktur()
    {
        return $this->belongsTo(Faktur::class, 'id_faktur');
    }

    // public function stok_batch(): BelongsTo
    // {
    //     return $this->belongsTo(StokBatchBarang::class, 'id_stok_batch_barang');
    // }
}
