<?php
namespace App\Models;

use App\Models\Barang\Barang;
use Illuminate\Database\Eloquent\Model;

class StokOpnameHistory extends Model
{
    protected $table    = 'stok_opname_histori';
    protected $fillable = [
        'id_barang',
        'stok',
        'stok_real',
        'kerugian',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
