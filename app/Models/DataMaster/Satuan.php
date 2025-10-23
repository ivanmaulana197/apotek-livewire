<?php

namespace App\Models\DataMaster;

use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Satuan extends Model
{
    use SoftDeletes, HasUlids, HasFactory, ScopeSearchable;
    protected $table = 'satuan';
    protected $fillable = ['nama'];
}
