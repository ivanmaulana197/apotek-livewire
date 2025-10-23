<?php

namespace App\Models\Promo;

use App\Models\Promo\Promo;
use App\Models\DataMaster\Profesi;
use Illuminate\Database\Eloquent\Model;

class PromoProfesiMember extends Model
{
     protected $table = 'promo_profesi_member';
     protected $fillable = [
        'id_promo', 'id_profesi',
    ];

    public $timestamps = false;

     public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function profesi()
    {
        return $this->belongsTo(Profesi::class);
    }
}
