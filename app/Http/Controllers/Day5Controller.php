<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

use Illuminate\Support\Str;

class Day5Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->boardingPasses = $this->inputRepository->getBoardingPasses();
        $this->seatsId = $this->boardingPasses->map(function ($boardingPass) {
            return $this->getSeatId($boardingPass);
        });
        return $this->seatsId->max();
    }

    public function part2() {
        $this->boardingPasses = $this->inputRepository->getBoardingPasses();
        $this->seatsId = $this->boardingPasses->map(function ($boardingPass) {
            return $this->getSeatId($boardingPass);
        });
        return $this->getEmptySeats();
    }

    public function getSeatId($boardingPass) {
        $rowChars = Str::substr($boardingPass, 0, 7);
        $colChars = Str::substr($boardingPass, 7, 3);
        $rowSeat = bindec(Str::of($rowChars)->replace('F', '0')->replace('B', '1'));
        $colSeat = bindec(Str::of($colChars)->replace('L', '0')->replace('R', '1'));
        return ($rowSeat * 8) + $colSeat;
    }

    public function getEmptySeats() {
        $fullSeatsId = collect(range($this->seatsId->min(), $this->seatsId->max()));
        return $fullSeatsId->diff($this->seatsId)->pop();
    }
}
