<?php

namespace App\Models\Faktur;

use App\Models\Barang\StokBatchBarang;
use App\Models\Barang\StokBatchFakturBarang;
use App\Models\HistoryBarang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DetailFakturBarang extends Model
{
    use HasUlids;
    protected $table = 'detail_faktur_barang';
    protected $fillable = ['id_history_barang','id_stok_batch_barang', 'harga_beli'];

    public function detailBarang(): BelongsTo   // ambil dari Table History Barang
    {
        return $this->belongsTo(HistoryBarang::class, 'id_history_barang');
    }

    public function stok_batch(): BelongsTo
    {
        return $this->belongsTo(StokBatchBarang::class, 'id_stok_batch_barang');
    }
}
