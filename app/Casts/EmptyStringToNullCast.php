<?php
namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class EmptyStringToNullCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return $value === '' ? null : $value;
    }
}
