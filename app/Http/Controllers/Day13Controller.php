<?php

namespace App\Http\Controllers;

use Log;

use App\Http\Repositories\InputRepository;

class Day13Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->additionalTime = -1;
        $this->timestamp = 402251700200000;
    }

    public function part1() {
        $this->notes = $this->inputRepository->getBusNotes();

        $buses = $this->notes->buses->reject(function ($busId) {
            return $busId === 'x';
        });

        $schedule = $buses->mapWithKeys(function ($busId) {
            return [$busId => $this->getNextDepart($busId)];
        });

        $nearestDepart = $schedule->min();

        return $nearestDepart * $schedule->search($nearestDepart);
    }

    public function part2() {
        $this->notes = $this->inputRepository->getBusNotes();

        $buses = $this->notes->buses; // ->reverse();

        $schedule = $buses->mapWithKeys(function ($busId) {
            $this->additionalTime++;
            return [$busId => $this->additionalTime];
        })->reject(function ($time, $busId) {
            return $busId === 'x';
        });

        // return self::crt($schedule);
        return $this->getTimestampByBruteForce($schedule);
    }

    public function getNextDepart($busDelay) {
        return $busDelay - ($this->notes->timestamp % $busDelay);
    }

    public function verifyNextDepart($busId, $additionalTime) {
        return ($this->timestamp + $additionalTime) % $busId === 0;
    }

    public function getTimestampByBruteForce($schedule) {
        $step = $schedule->search($schedule->first());

        do {
            $this->timestamp += $step;
            $timestampFound = $schedule->every(function ($additionalTime, $busId) {
                return $this->verifyNextDepart($busId, $additionalTime);
            });
        } while (!$timestampFound);

        return $this->timestamp;
    }
}
