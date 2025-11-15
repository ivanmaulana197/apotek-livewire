<?php
namespace App\Models;

use App\Helpers\FormatHelper;
use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use App\Models\Faktur\DetailFakturBarang;
use App\Models\Transaksi\DetailTransaksiBarang;
use App\Models\Transaksi\Transaksi;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HistoryBarang extends Model
{
    use HasFactory, HasUlids;
    protected $table = 'history_barang';

    protected $fillable = ['id_barang', 'jumlah', 'id_satuan', 'status', 'tgl_expired', 'id_user'];

    protected $casts = [
        'tgl_expired' => 'array', // otomatis jadi array saat diambil dari DB
    ];
    /* tgl_expired = {[ 'tgl' => '2023-01-01', 'qty' => 10 ]} */

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // public function referensi(): MorphTo
    // {
    //     return $this->morphTo();
    // }

    public function detail_faktur(): HasOne
    {
        return $this->hasOne(DetailFakturBarang::class, 'id_history_barang');
    }

    public function detail_transaksi(): HasOne
    {
        return $this->hasOne(DetailTransaksiBarang::class, 'id_history_barang');
    }

    public function transaksi()
    {
        // lewat detail_transaksi_barang
        return $this->hasOneThrough(
            Transaksi::class,
            DetailTransaksiBarang::class,
            'id_history_barang', // Foreign key di tabel detail_transaksi_barang
            'id',                // Foreign key di tabel transaksi
            'id',                // Local key di tabel history_barang
            'id_transaksi'       // Local key di tabel detail_transaksi_barang
        );
    }

    public function getJumlahDalamSatuanUtamaAttribute()
    {
        $barang = $this->barang;
        if ($barang->id_satuan_utama == $this->id_satuan) {
            return $this->jumlah;
        }
        //cara 1:
        $pivot = $this->barang->opsi_satuan->firstWhere('id', $this->id_satuan);
        if ($pivot) {
            return $this->jumlah * (
                $pivot->pivot->rasio_satuan_utama / $pivot->pivot->rasio_satuan_opsi
            );
        }
        return null;
        // fallback kalau tidak ada pivot
        // cara 2::
        // else{
        //     $satuan_opsi = $barang->satuan_opsi->where('id_satuan', $this->id_satuan)->first();
        //     if($satuan_opsi){
        //         return $this->jumlah * $satuan_opsi->rasio;
        //     }else{
        //         return 0;
        //     }
        // }
    }

    public function print_tgl_expired($with_qty = false)
    {
        foreach ($this->tgl_expired as $item) {
            $tgl = FormatHelper::formatDate($item['tgl']);
            if ($with_qty) {
                $jumlah = FormatHelper::formatNumberToString($item['jumlah']);
                echo "- $tgl ($jumlah / {$this->barang->satuan_utama->nama}) <br>";
            } else {
                echo "- $tgl <br>";
            }
        }
    }

}