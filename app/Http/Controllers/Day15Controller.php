<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

use Illuminate\Support\Facades\Log;

class Day15Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->numbers = $this->inputRepository->getStartingNumbers();
        $this->playMemory(2020);
        return $this->numbers->last();
    }

    public function part2() {
        $this->numbers = $this->inputRepository->getStartingNumbers();
        $this->playMemory(30000000);
        return $this->numbers->last();
    }

    public function playMemory($upTo) {
        for ($i = 0; $i < $upTo; $i++) {
            if ($this->numbers->has($i)) continue;
            
            $nextTurn = $i + 1;
            $nextNumber = 0;
            $lastTurn = $i - 1;
            $lastNumber = $this->numbers->last();

            if ($this->alreadySpoken($lastNumber)) {
                $beforeLastTurn = $this->getTurnWhereNumberWasSpokenBefore($lastNumber, $lastTurn);
                $nextNumber = ($this->wasSpokenTwiceInARow($beforeLastTurn, $lastTurn)) ? 1 : ($lastTurn + 1) - ($beforeLastTurn + 1);
            }

            $this->numbers->put($i, $nextNumber);
            Log::info('Spoken..', ['turn' => $nextTurn, 'number' => $nextNumber]);
        }
    }

    public function alreadySpoken($lastNumber) {
        return ($this->numbers->countBy()->get($lastNumber) > 1);
    }

    public function wasSpokenTwiceInARow($beforeLastTurn, $lastTurn) {
        return ($beforeLastTurn === $lastTurn - 1);
    }

    public function getTurnWhereNumberWasSpokenBefore($lastNumber, $lastTurn) {
        return $this->numbers->reverse()->search(function ($number, $turn) use ($lastNumber, $lastTurn) {
            return $number === $lastNumber && $turn !== $lastTurn;
        });
    }
}
