<?php

namespace App\Models\DataMaster;

use App\Models\Barang\Barang;
use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rak extends Model
{
    use SoftDeletes, HasUlids, HasFactory, ScopeSearchable;
    protected $table = 'rak';
    protected $fillable = ['nama'];

    public function barang()
    {
        $this->belongsToMany(Barang::class,'rak_barang','id_rak','id_barang');
    }
}
