<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasUlids;

    protected $table = 'setting';

    protected $fillable = [
        'nama',
        'alamat',
        'kabupaten',
        'email',
        'no_telp',
        'link_website',
        'link_tiktok',
        'link_instagram',
        'link_facebook',
        'link_google_map',
        'logo',
        'pembulatan_harga_barang',
        'pembulatan_resep',
        'pembulatan_total',
        'persentase_jual',
        'persentase_jual_resep',
        'footer_struk'
    ];
}
