<?php

namespace App\Models\Spatie;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
// use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;
    use HasUlids;
    // protected $primaryKey = 'uuid';
}
