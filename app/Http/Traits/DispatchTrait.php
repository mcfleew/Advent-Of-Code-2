<?php

namespace App\Http\Traits;

trait DispatchTrait
{
    public function __invoke($part = 1)
    {
        if ($part == 1) return $this->part1();
        if ($part == 2) return $this->part2();
    }
}