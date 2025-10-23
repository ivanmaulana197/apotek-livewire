<?php

namespace App\Models\Promo;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PromoTransaksi extends Model
{
    use HasUlids;

    protected $table = 'promo_transaksi';
    protected $fillable = [
        'id_promo', 'minimal_belanja', 'tipe_transaksi', 'diskon_persen', 'diskon_nominal'
    ];

    public function promo()
    {
        return $this->belongsTo(Promo::class,'id_promo');
    }
}
