<?php

namespace App\Traits;

trait CleansEmptyStrings
{
    public function updating($property, $value)
    {
        dd($property, $value);
        if ($value === '') {
            $this->$property = null;
        }
    }
}