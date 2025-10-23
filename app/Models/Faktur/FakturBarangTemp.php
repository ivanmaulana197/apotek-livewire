<?php

namespace App\Models\Faktur;

use App\Models\User;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class FakturBarangTemp extends Model
{
    use HasUlids;
    protected $table = "faktur_barang_temp";

    protected $fillable = [
        'id_barang',
        'jumlah',
        'id_satuan',
        'tgl_expired',
        'harga_beli',
        'id_user'
    ];

    public function user(){
        return $this->belongsTo(User::class,'id_user');
    }

    public function barang(){
        return $this->belongsTo(Barang::class,'id_barang');
    }

    public function satuan(){
        return $this->belongsTo(Satuan::class,'id_satuan');
    }

    public function getJumlahDalamSatuanUtamaAttribute()
    {
        // Jika satuan = satuan utama barang
        if ($this->id_satuan == $this->barang->id_satuan_utama) {
            return $this->jumlah;
        }
        // Cari pivot konversi
        $pivot = $this->barang->opsi_satuan->firstWhere('id', $this->id_satuan);
        if ($pivot) {
            return $this->jumlah * (
                $pivot->pivot->rasio_satuan_utama / $pivot->pivot->rasio_satuan_opsi
            );
        }

        return null; // fallback kalau tidak ada pivot
    }

}
