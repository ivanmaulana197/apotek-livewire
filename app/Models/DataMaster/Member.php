<?php
namespace App\Models\DataMaster;

use App\Models\DataMaster\Profesi;
use App\Models\Transaksi\Transaksi;
use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\DataMaster\MemberFactory> */
    use HasFactory, HasUlids, ScopeSearchable;

    protected $table    = 'member';
    protected $fillable = ['nama', 'alamat', 'no_hp', 'id_profesi', 'id_user'];

    public function profesi()
    {
        return $this->belongsTo(Profesi::class, 'id_profesi');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_member');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['id_profesi'] ?? null, function ($query, $id_profesi) {
            $query->where('id_profesi', $id_profesi);
        });
    }
}
