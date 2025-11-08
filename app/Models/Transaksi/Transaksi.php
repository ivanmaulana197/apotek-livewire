<?php
namespace App\Models\Transaksi;

use App\Models\DataMaster\Dokter;
use App\Models\DataMaster\Member;
use App\Models\DataMaster\MetodePembayaran;
use App\Models\DataMaster\Pasien;
use App\Models\HistoryBarang;
use App\Models\Transaksi\DetailTransaksiBarang;
use App\Models\Transaksi\TransaksiListPromo;
use App\Models\User;
use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasUlids, ScopeSearchable;

    protected $table    = 'transaksi';
    protected $fillable = [
        'no_transaksi',
        'sub_total',
        'potongan_diskon',
        'total_bayar',
        'pembulatan',
        'bayar',
        'kembalian',
        'id_user',
        'id_metode_pembayaran',
        'jenis_transaksi',
        'shift_transaksi',
        'status',
        'id_member',
        'id_pasien',
        'id_dokter',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function metode_pembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'id_metode_pembayaran');
    }
    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member');
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'id_pasien');
    }
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'id_dokter');
    }

    // public function transaksi_barang()
    // {
    //     return $this->morphMany(HistoryBarang::class, 'referensi');
    // }

    public function detail_transaksi()
    {
        return $this->hasMany(DetailTransaksiBarang::class, 'id_transaksi');
    }

    public function detail_transaksi_barang()
    {
        return $this->hasManyThrough(
            HistoryBarang::class,
            DetailTransaksiBarang::class,
            'id_transaksi',     // Foreign key di tabel detail_transaksis
            'id',               // Foreign key di tabel history_barangs
            'id',               // Local key di tabel transaksis
            'id_history_barang' // Local key di tabel detail_transaksis
        );

    }

    // public function detailTransaksiBarang()
    // {
    //     return $this->hasManyThrough(
    //         DetailTransaksiBarang::class,
    //         HistoryBarang::class,
    //         'referensi_id',      // Foreign key di tabel history_barang
    //         'id_history_barang', // Foreign key di tabel detail_transaksi_barang
    //         'id',                // Local key di tabel transaksi
    //         'id'                 // Local key di tabel history_barang
    //     )->where('history_barang.referensi_type', self::class);
    // }

    public function transaksi_list_promo()
    {
        return $this->hasMany(TransaksiListPromo::class, 'id_transaksi');
    }
}
