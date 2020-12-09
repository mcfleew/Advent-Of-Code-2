<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day9Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->lengthPreamble = 25;
        $this->preamble = collect();
    }

    public function part1() {
        $this->numbers = $this->inputRepository->getNumbersList();
        for ($counter = 0; $counter < $this->numbers->count(); $counter++) {
            $number = $this->numbers->get($counter);

            if ($counter >= $this->lengthPreamble) {
                $sumMatrixRaw = $this->preamble->crossJoin($this->preamble);
                $sumMatrix = $sumMatrixRaw->reject(function ($m) {
                    return $m[0] === $m[1];
                });
                $sum = $sumMatrix->first(function ($m) use ($number) {
                    return $m[0] + $m[1] === $number;
                });
                
                if (!$sum) {
                    return $number;
                }
                $this->preamble->shift();
            }
            $this->preamble->push($number);
        }
        return 0;
    }

    public function part2() {
        $this->numbers = $this->inputRepository->getNumbersList();
        for ($counter = 0; $counter < $this->numbers->count(); $counter++) {
            $number = $this->numbers->get($counter);

            if ($counter >= $this->lengthPreamble) {
                $sumMatrixRaw = $this->preamble->crossJoin($this->preamble);
                $sumMatrix = $sumMatrixRaw->reject(function ($m) {
                    return $m[0] === $m[1];
                });
                $sum = $sumMatrix->first(function ($m) use ($number) {
                    return $m[0] + $m[1] === $number;
                });
                
                if (!$sum) {
                    return $number;
                }
                $this->preamble->shift();
            }
            $this->preamble->push($number);
        }
        return 0;
    }
}
