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
   
    /*
    @author MALIK7934
    CHINESE REMAINDER THEOREM
    théorème des restes chinois
    */
    public static function crt($a,$m,$b,$n) {
        $res = ($a*$n*self::eea($n,$m)+$b*$m*self::eea($m,$n))%($m*$n);

        if ($res<0){
            while ($res<0)
                $res += $m*$n;
        }

        return $res;
    }

    // extended Euclid Algorithm pour le calcul de l'inverse
    public static function eea($a,$b) {
        $x = array($a,1,0);
        $y = array($b,0,1);

        while ($y[0] > 0){
            $rq = self::ed($x[0],$y[0]);
            $temp = $x;
            $x = $y;
            $y[0] = $temp[0] - $rq[1]*$y[0];
            $y[1] = $temp[1] - $rq[1]*$y[1];
            $y[2] = $temp[2] - $rq[1]*$y[2];
        }
        
        return $x[1];
    }

    // division euclidienne
    public static function ed($x,$y) {
        $rq[0] = $x % $y;
        $rq[1] = ($x-$rq[0])/$y;

        return $rq;
    }
}