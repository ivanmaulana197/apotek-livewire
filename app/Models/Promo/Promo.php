<?php

namespace App\Models\Promo;

use App\Traits\ScopeSearchable;
use App\Models\Promo\PromoBarang;
use App\Models\DataMaster\Profesi;
use App\Models\Promo\PromoTransaksi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Promo extends Model
{
    use HasUlids,ScopeSearchable;

    protected $table = 'promo';
    protected $fillable = [
        'id',
        'nama',
        'deskripsi',
        'tipe',
        'start_date',
        'end_date',
        'is_aktif',
        'is_member',
    ];

    public function promoBarang()
    {
        return $this->hasMany(PromoBarang::class, 'id_promo');
    }

    public function promoTransaksi()
    {
        return $this->hasOne(PromoTransaksi::class, 'id_promo');
    }

    public function profesies()
    {
        return $this->belongsToMany(Profesi::class, 'promo_profesi_member', 'id_promo', 'id_profesi');
    }

    public function getProfesiListAttribute()
    {
        $profesi = $this->profesies->pluck('nama');
        if ($profesi->count() > 2) {
            // lebih dari 2 profesi → "A, B dan C"
            return $profesi->join(', ', ', dan ');
        } elseif ($profesi->count() === 2) {
            // tepat 2 profesi → "A dan B"
            return $profesi->implode(' dan ');
        } else {
            // hanya 1 atau tidak ada
            return $profesi->first() ?? '-';
        }
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true)->whereDate('start_date', '<=', now())->where(function ($q) {
            $q->whereNull('end_date')->orWhereDate('end_date', '>=', now());
        });
    }

    public function scopeFilter($query, array $filters)
    {
        $query
            ->when($filters['is_aktif'] ?? null, function ($query, $is_aktif) {
                $is_aktif = ($is_aktif == "AKTIF") ? 1 : ($is_aktif == "NON_AKTIF" ? 0 : $is_aktif);
                $query->where('is_aktif', $is_aktif);
            })
            ->when($filters['id_profesies'] ?? null, function ($query, $id_profesi) {
                $query->whereHas('profesies', function ($q) use ($id_profesi) {
                    $q->whereIn('profesi.id', $id_profesi);
                }, '=', count($id_profesi)); //menampilkan rak yang harus terpilih
            })
            ->when($filters['date'] ?? null, function ($query, $date) {
                $query->whereDate('start_date', '<=', $date)->where(function ($q) use ($date) {
                    $q->whereNull('end_date')->orWhereDate('end_date', '>=', $date);
                });
            })
        ;
    }
}