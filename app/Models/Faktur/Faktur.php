<?php

namespace App\Models\Faktur;

use App\Models\Barang\Barang;
use App\Models\User;
use App\Traits\ScopeSearchable;
use App\Models\DataMaster\Supplier;
use App\Models\HistoryBarang;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Faktur extends Model
{
    use HasFactory, HasUlids, ScopeSearchable;
    protected $table = 'faktur';

    protected $fillable = ['no_faktur', 'id_supplier', 'total_biaya', 'tgl_jatuh_tempo', 'status', 'tgl_masuk', 'id_user'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_supplier');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function faktur_barang()
    {
        return $this->morphMany(HistoryBarang::class, 'referensi');
    }

    protected static function booted()
    {
        static::creating(function ($faktur) {
            $faktur->id_user = Auth::user()->id;
            $faktur->tgl_masuk = now();
            if ($faktur->tgl_jatuh_tempo == null) {
                $faktur->tgl_jatuh_tempo = now();
            }
        });
        static::deleting(function ($faktur) {

            // Hapus semua barang masuk terkait faktur (jika masih ada yang tertinggal)
            // $faktur->delete();
        });
    }

    public function scopeFilter($query, $filters)
    {
        foreach ($filters as $key => $value) {
            $query->when($value ?? null, function ($query, $item) use ($key) {
                if ($key == 'tgl_jatuh_tempo' || $key == 'created_at') {
                    $query->whereBetween("faktur.$key", ["{$item['start']} 00:00:00", "{$item['end']} 23:59:59"]);
                } else {
                    $query->where("faktur.$key", $item);
                }
            });
        }
        
    }
}
