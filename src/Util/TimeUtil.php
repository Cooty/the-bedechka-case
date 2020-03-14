<?php


namespace App\Util;


class TimeUtil
{
    public static function getFormattedNow(string $format = 'Y-m-d'): string
    {
        $now = new \DateTime();
        return $now->format($format);
    }

    public static function addLeadingZero(int $decimal): string
    {
        if($decimal < 10) {
            return '0'.$decimal;
        } else {
            return (string)$decimal;
        }
    }
}