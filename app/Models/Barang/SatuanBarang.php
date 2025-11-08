<?php
namespace App\Models\Barang;

use App\Models\DataMaster\Satuan;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatuanBarang extends Model
{
    use HasUlids, HasFactory;
    protected $table    = 'satuan_barang';
    protected $fillable = ['id_barang', 'id_satuan', 'rasio_satuan_utama', 'rasio_satuan_opsi'];
    public $timestamps  = false;

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

}
