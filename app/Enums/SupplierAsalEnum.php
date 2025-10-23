<?php

namespace App\Enums;

enum SupplierAsalEnum : string
{
    case PT = 'PT';
    case CV = 'CV';
    case UD = 'UD';

    /**
     * Mengembalikan array asosiatif dari enum.
     *
     * @return array
     */
    public static function toArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[] = ['value' => $case->value];
        }
        return $result;
    }
}