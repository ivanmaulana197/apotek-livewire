<?php
namespace App\Models\Promo;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use App\Models\Promo\Promo;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PromoBarang extends Model
{
    use HasUlids;
    protected $table    = 'promo_barang';
    protected $fillable = [
        'id_promo', 'id_barang', 'id_satuan', 'syarat_qty',
        'tipe', 'tipe_qty',
        'id_barang_bonus', 'id_satuan_bonus', 'bonus_qty',
        'diskon_persen', 'diskon_nominal',
    ];

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'id_promo');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function satuan_bonus()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan_bonus');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function barang_bonus()
    {
        return $this->belongsTo(Barang::class, 'id_barang_bonus');
    }

    public function getSyaratQtyDalamSatuanUtamaAttribute()
    {
        $barang = $this->barang;
        // dd($barang);
        if ($barang->id_satuan_utama == $this->id_satuan) {
            return $this->syarat_qty;
        }
        //cara 1:
        $pivot = $barang->opsi_satuan->firstWhere('id', $this->id_satuan);
        // dd($pivot);
        if ($pivot) {
            return $this->syarat_qty * (
                $pivot->pivot->rasio_satuan_utama / $pivot->pivot->rasio_satuan_opsi
            );
        }
        return null;
    }

    public function getBonusQtyDalamSatuanUtamaAttribute()
    {
        $barang_bonus = $this->barang_bonus;
        // dd($barang_bonus);
        if ($barang_bonus->id_satuan_utama == $this->id_satuan_bonus) {
            return $this->bonus_qty;
        }
        //cara 1:
        $pivot = $this->barang_bonus->opsi_satuan->firstWhere('id', $this->id_satuan_bonus);
        // dd($pivot);
        if ($pivot) {
            return $this->bonus_qty * (
                $pivot->pivot->rasio_satuan_utama / $pivot->pivot->rasio_satuan_opsi
            );
        }
        return null;
    }
}
