<?php

namespace App\Models\DataMaster;

use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    /** @use HasFactory<\Database\Factories\DataMaster\DokterFactory> */
    use HasFactory, HasUlids, ScopeSearchable;

    protected $table = 'dokter';
    protected $fillable = ['nama','alamat','no_hp','spesialis'];

}
