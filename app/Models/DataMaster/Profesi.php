<?php

namespace App\Models\DataMaster;

use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesi extends Model
{
    /** @use HasFactory<\Database\Factories\DataMaster\ProfesiFactory> */
    use HasFactory, HasUlids, ScopeSearchable;

    protected $table = 'profesi';
    protected $fillable = ['nama'];

    public function members(){
        return $this->hasMany(Member::class, 'id_member');
    }
}