<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

use App\Http\Traits\DispatchTrait;
use App\Http\Traits\DebugTrait;
use App\Http\Traits\UtilTrait;

class Controller extends BaseController
{
    use DispatchTrait, DebugTrait, UtilTrait;

    public function __construct()
    {
        //
    }

    public function part1()
    {
        //
    }

    public function part2()
    {
        //
    }
}
