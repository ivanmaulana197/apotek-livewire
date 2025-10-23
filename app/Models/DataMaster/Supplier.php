<?php
namespace App\Models\DataMaster;

use App\Models\Faktur\Faktur;
use App\Traits\ScopeSearchable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, HasUlids, ScopeSearchable;
    protected $table = 'supplier';

    protected $fillable = ['nama', 'asal', 'alamat', 'no_hp', 'is_pajak'];

    protected $casts = [
        'is_pajak' => 'boolean',
        // 'asal' => SupplierAsalEnum::class,
    ];

    public function fakturs()
    {
        return $this->hasMany(Faktur::class, 'id_supplier');
    }

    public function setAlamatAttribute($value)
    {
        $this->attributes['alamat'] = $value === '' ? null : $value;
    }
    public function setNoHpAttribute($value)
    {
        $this->attributes['no_hp'] = $value === '' ? null : $value;
    }
}
