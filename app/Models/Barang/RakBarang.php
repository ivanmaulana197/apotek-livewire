<?php

namespace App\Models\Barang;

use App\Models\DataMaster\Rak;
use Illuminate\Database\Eloquent\Model;

class RakBarang extends Model
{
    protected $table = 'rak_barang';

    protected $fillable = [
        'id_rak',
        'id_barang',
    ];

    public $timestamps = false;

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'id_rak');
    }
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}