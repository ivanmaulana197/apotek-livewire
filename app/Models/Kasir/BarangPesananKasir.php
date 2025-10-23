<?php
namespace App\Models\Kasir;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use App\Models\Kasir\PesananKasir;
use App\Models\Promo\PromoBarang;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class BarangPesananKasir extends Model
{
    use HasUlids;

    protected $table    = 'barang_pesanan_kasir';
    protected $fillable = [
        'id_pesanan_kasir',
        'id_barang',
        'jumlah',
        'id_satuan',
        'diskon',
        // 'id_promo_barang',
        'harga',
        'harga_satuan_utama',
        'sub_total'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function pesanan_kasir()
    {
        return $this->belongsTo(PesananKasir::class, 'id_pesanan_kasir');
    }
    public function promo_barang()
    {
        return $this->belongsTo(PromoBarang::class, 'id_promo_barang');
    }

    public function getJumlahDalamSatuanUtamaAttribute()
    {
        // $this->barang()->satuan_opsi();
        $barang = $this->barang;
        // dd($barang);
        if ($barang->id_satuan_utama == $this->id_satuan) {
            return $this->jumlah;
        }
        //cara 1:
        $opsi_satuan = $this->barang->opsi_satuan->firstWhere('id', $this->id_satuan);
        // dd($pivot);
        if ($opsi_satuan) {
            return $this->jumlah * (
                $opsi_satuan->pivot->rasio_satuan_utama / $opsi_satuan->pivot->rasio_satuan_opsi
            );
        }
        return null; // fallback kalau tidak ada pivot
    }

    public function scopeTotalJumlahDalamSatuanUtama($query, $id_barang, $jumlah_dalam_satuan_utama = 0, $except_id = null)
    {
        // dd($jumlah_dalam_satuan_utama);
        $total = $query->where('id_barang', $id_barang)
            ->when($except_id, function ($query, $except_id) {
                $query->where('id', '!=', $except_id);
            })
            ->get()
            ->sum(fn($item) => $item->jumlah_dalam_satuan_utama);
        return $total + $jumlah_dalam_satuan_utama;
    }

    public function scopeTotalHargaBarang($query, $id_barang, $except_id = null)
    {
        $total = $query->where('id_barang', $id_barang)
            ->when($except_id, function ($query, $except_id) {
                $query->where('id', '!=', $except_id);
            })
            ->get()
            ->sum(fn($item) => $item->sub_total);
        return $total;
    }
}
