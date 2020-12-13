<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day13Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->notes = $this->inputRepository->getBusNotes();

        $schedule = $this->notes->buses->mapWithKeys(function ($busId) {
            return [$busId => $this->getNextDepart($busId)];
        });

        $nearestDepart = $schedule->min();

        return $nearestDepart * $schedule->search($nearestDepart);
    }

    public function part2() {
        $this->notes = $this->inputRepository->getBusNotes();

        return $this->notes->buses;
    }

    public function getNextDepart($busDelay) {
        return $busDelay - ($this->notes->timestamp % $busDelay);
    }
}
