<?php
namespace App\Models\Barang;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Satuan;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaBarang extends Model
{
    use HasFactory, HasUlids;
    protected $table    = 'harga_barang';
    protected $fillable = [
        'id',
        'id_barang',
        'jumlah_min',
        'id_satuan',
                // 'is_default', // UTAMA, OPSI
        'mode', // PERSEN, HARGA
        'persen',
        'harga',
        'harga_satuan_utama',
    ];

    public $timestamps = false;

    // protected $appends = ['jumlah_dalam_satuan_utama'];
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function getJumlahDalamSatuanUtamaAttribute()
    {
        // Jika satuan = satuan utama barang
        if ($this->id_satuan == $this->barang->id_satuan_utama) {
            return $this->jumlah_min;
        }
        // Cari pivot konversi
        $pivot = $this->barang->opsi_satuan->firstWhere('id', $this->id_satuan);
        if ($pivot) {
            return $this->jumlah_min * (
                $pivot->pivot->rasio_satuan_utama / $pivot->pivot->rasio_satuan_opsi
            );
        }

        return null; // fallback kalau tidak ada pivot
    }

}
