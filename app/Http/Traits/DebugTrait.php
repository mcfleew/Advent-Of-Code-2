<?php

namespace App\Http\Traits;

trait DebugTrait
{
    public static $log;
    
    public static function debug($var) {
        echo '<pre>';
        if (is_array($var)) {
            print_r($var);
        } else {
            var_dump($var);
        }
    }
    
    public static function debugAndDie($var) {
        self::debug($var);
        exit;
    }
    
    public static function debugMatrix($var) {
        echo '<pre>';
        $var->each(function ($row) {
            echo $row->implode('').PHP_EOL;
        });
    }
}