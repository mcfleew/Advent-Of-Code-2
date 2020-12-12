<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day11Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->seatsMap = $this->inputRepository->getSeatsMap();
        $this->seatsMapBuffer = $this->inputRepository->getSeatsMap();

        do {
            $this->atLeastOnePersonMoved = false;
            $this->applyOneRule();
            $this->applyOtherRule();
            $this->flushSeatsMap();
        } while ($this->atLeastOnePersonMoved);

        return $this->getAllOccupiedSeatsCount(); 
    }

    public function part2() {
        $this->seatsMap = $this->inputRepository->getSeatsMap();
        return $this->seatsMap;
    }

    public function applyOneRule() {
        foreach($this->seatsMap as $keyRow => $seats) {
            foreach($seats as $keyCol => $seat) {
                if ($seat === 'L') {
                    $adjacentSeats = $this->getAdjacentSeats($keyRow, $keyCol);
                    $occupiedSeatsCounter = $this->countOccupiedSeats($adjacentSeats);

                    if ($occupiedSeatsCounter === 0) {
                        $this->setSeat($keyRow, $keyCol, '#');
                    }
                }
            }
        }
    }

    public function applyOtherRule() {
        foreach($this->seatsMap as $keyRow => $seats) {
            foreach($seats as $keyCol => $seat) {
                if ($seat === '#') {
                    $adjacentSeatsCoordinates = $this->getAdjacentSeats($keyRow, $keyCol);
                    $occupiedSeatsCounter = $this->countOccupiedSeats($adjacentSeatsCoordinates);

                    if ($occupiedSeatsCounter >= 4) {
                        $this->setSeat($keyRow, $keyCol, 'L');
                    }
                }
            }
        }
    }

    public function flushSeatsMap() {
        $this->seatsMap = $this->seatsMapBuffer->map(function ($seats) {
            return collect($seats);
        });
    }

    public function getAllOccupiedSeatsCount() {
        $allCoordinates = collect(range(0, count($this->seatsMap)))->crossJoin(range(0, count($this->seatsMap[0])));
        return $this->countOccupiedSeats($allCoordinates);
    }

    public function getAdjacentSeats($keyRow, $keyCol) {
        $coordinates = collect([$keyRow-1, $keyRow, $keyRow+1])->crossJoin([$keyCol-1, $keyCol, $keyCol+1]);
        return $coordinates->map(function ($coordinate) use ($keyRow, $keyCol) {
            if (!($coordinate[0] === $keyRow && $coordinate[1] === $keyCol)) {
                return collect($coordinate);
            }
        });
    }
    
    public function countOccupiedSeats($adjacentSeatsCoordinates) {
        $occupiedSeatsCounter = 0;

        foreach($adjacentSeatsCoordinates as $adjacentSeatCoordinates) {
            $seat = $this->getSeatFromSeatsMap($adjacentSeatCoordinates);
            if ($seat && $seat === '#') $occupiedSeatsCounter++;
        }

        return $occupiedSeatsCounter;
    }

    public function getSeatFromSeatsMap($coordinates) {
        return collect($this->seatsMap->get($coordinates[0]))->get($coordinates[1]);
    }

    public function setSeat($keyRow, $keyCol, $value) {
        $this->seatsMapBuffer->transform(function ($seats, $seatsIdx) use ($keyRow, $keyCol, $value) {
            if ($seatsIdx === $keyRow) {
                $seats->put($keyCol, $value);
            }
            return $seats;
        });
        $this->atLeastOnePersonMoved = true;
    }
}