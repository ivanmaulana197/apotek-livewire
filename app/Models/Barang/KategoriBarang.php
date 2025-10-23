<?php
namespace App\Models\Barang;

use App\Models\Barang\Barang;
use App\Models\DataMaster\Kategori;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class KategoriBarang extends Model
{
    use HasUlids;
    protected $table    = 'kategori_barang';
    protected $fillable = ['id_kategori', 'id_barang'];
    public $timestamps  = false;
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
