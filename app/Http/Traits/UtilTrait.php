<?php

namespace App\Http\Traits;

trait UtilTrait
{
    public static function product($carry, $item)
    {
        $carry *= $item;
        return $carry;
    }

    public static function array_product($input) {
        return array_reduce($input, 'self::product', 1);
    }

    public static function charAt($string, $position) {
        return substr($string, $position, 1);
    }

    public static function getCharInstances($string) {
        $charsInstances = array_count_values(str_split($string));
        return collect($charsInstances);
    }
}