<?php

namespace App\Models\DataMaster;

use App\Models\Barang\Barang;
use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes, HasUlids, HasFactory, ScopeSearchable;
    protected $table = 'kategori';
    protected $fillable = ['nama'];

    public function barangs()
    {
        return $this->hasMany(Barang::class,'id_kategori');
    }
}