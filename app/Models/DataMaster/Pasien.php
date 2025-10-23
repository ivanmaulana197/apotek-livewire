<?php

namespace App\Models\DataMaster;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    /** @use HasFactory<\Database\Factories\DataMaster\PasienFactory> */
    use HasFactory, HasUlids;

    protected $table = 'pasien';
    protected $fillable = ['nama_pasien','alamat_pasien','no_hp_pasien'];
}
