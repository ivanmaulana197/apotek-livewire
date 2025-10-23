<?php

namespace App\Models\DataMaster;

use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    /** @use HasFactory<\Database\Factories\DataMaster\MetodePembayaranFactory> */
    use HasFactory, HasUlids, ScopeSearchable;

    protected $table = 'metode_pembayaran';
    protected $fillable = ['nama'];
}
