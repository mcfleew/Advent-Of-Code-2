<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day9Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->lengthPreamble = 25;
        $this->preamble = collect();
        $this->range = collect();
    }

    public function part1() {
        $this->numbers = $this->inputRepository->getNumbersList();
        return $this->findAnomalyNumber();
    }

    public function part2() {
        $this->numbers = $this->inputRepository->getNumbersList();
        
        $anomaly = $this->findAnomalyNumber();
        return $this->findContiguousAnomalyRange($anomaly);
    }

    public function findAnomalyNumber() {
        for ($counter = 0; $counter < $this->numbers->count(); $counter++) {
            $number = $this->numbers->get($counter);

            if ($counter >= $this->lengthPreamble) {
                $sumMatrix = $this->getAllSumPossibilities();
                $sumMatch = $this->findSumThatMatchNumber($sumMatrix, $number);

                if (!$sumMatch) {
                    return $number;
                }
                $this->preamble->shift();
            }
            $this->preamble->push($number);
        }
        return 0;
    }

    public function findContiguousAnomalyRange($anomaly) {
        for ($counter = 0; $counter < $this->numbers->count(); $counter++) {
            $number = $this->numbers->get($counter);

            $this->sumEveryNumber($number);
            $this->shiftNumbersIfExceedAnomaly($anomaly);

            if ($this->range->sum() === $anomaly) {
                return $this->range->min() + $this->range->max();
            }
        }
        return 0;
    }

    public function getAllSumPossibilities() {
        $sumMatrix = $this->preamble->crossJoin($this->preamble);
        return $sumMatrix->reject(function ($m) {
            return $m[0] === $m[1];
        });
    }

    public function findSumThatMatchNumber($sumMatrix, $number) {
        return $sumMatrix->first(function ($m) use ($number) {
            return $m[0] + $m[1] === $number;
        });
    }

    public function sumEveryNumber($number) {
        $this->range->push($number);
    }

    public function shiftNumbersIfExceedAnomaly($anomaly) {
        while ($this->range->sum() > $anomaly) {
            $this->range->shift();
        }
    }
}
