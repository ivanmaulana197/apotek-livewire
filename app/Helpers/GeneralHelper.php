<?php
namespace App\Helpers;

use Carbon\Carbon;

class GeneralHelper
{
    public static function checkModeShift()
    {
        $currentHour = Carbon::now()->hour;
        return $currentHour < 14 ? 'PAGI' : 'MALAM';
    }
}
