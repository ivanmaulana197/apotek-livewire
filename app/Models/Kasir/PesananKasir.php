<?php

namespace App\Models\Kasir;

use App\Models\User;
use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\Member;
use Illuminate\Database\Eloquent\Model;
use App\Models\DataMaster\MetodePembayaran;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class PesananKasir extends Model
{
    use HasUlids;

    protected $table = 'pesanan_kasir';
    protected $fillable = [
        'no_pesanan_kasir',
        'id_member',
        'mode_kasir',
        'mode_shift',
        'total_harga',
        'id_metode_pembayaran',
        'id_user',
        'id_dokter',
        'nama_pasien',
        'alamat_pasien',
        'no_hp_pasien'
    ];

    public function member(){
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function metode_pembayaran(){
        return $this->belongsTo(MetodePembayaran::class, 'id_metode_pembayaran');
    }

    public function user(){
        return $this->belongsTo(User::class, 'id_user');
    }

    public function dokter(){
        return $this->belongsTo(Dokter::class, 'id_dokter');
    }

    public function barang_pesanan_kasir()
    {
        return $this->hasMany(BarangPesananKasir::class, 'id_pesanan_kasir');
    }

    public function promo_pesanan_kasir()
    {
        return $this->hasMany(PromoPesananKasir::class, 'id_pesanan_kasir');
    }

}