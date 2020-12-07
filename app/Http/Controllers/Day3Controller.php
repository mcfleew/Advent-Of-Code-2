<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day3Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->charTree = '#';
    }
    
    public function part1() {
        $map = $this->inputRepository->getOpenSquaresAndTrees();
        return $this->checkSlope($map, 3, 1);
    }

    public function part2() {
        $map = $this->inputRepository->getOpenSquaresAndTrees();
        return self::array_product([
            $this->checkSlope($map, 1),
            $this->checkSlope($map, 3),
            $this->checkSlope($map, 5),
            $this->checkSlope($map, 7),
            $this->checkSlope($map, 1, 2)
        ]);
    }

    public function checkSlope($map, $rightStep, $downStep = 1) {
        $this->rightStep = $rightStep;
        $this->downStep = $downStep;
        $this->mapCol = 0;
        $this->mapGrower = 1;

        $mapRowsWithTrees = $map->filter(function ($mapRow, $mapIdx) {
            if ($this->checkThisRow($mapIdx)) {
                $this->getNextPosition($mapIdx); 

                if ($this->isOutOfMap($mapRow)) {
                    $mapRow = $this->extendMapRow($mapRow);
                }
                return $this->isThereATree($mapRow);
            }
        });
        
        return $mapRowsWithTrees->count();
    }

    public function getNextPosition($mapIdx) {
        if ($mapIdx > 0) $this->mapCol += $this->rightStep;
    }

    public function checkThisRow($mapIdx) {
        return ($mapIdx % $this->downStep === 0);
    }

    public function isOutOfMap($mapRow) {
        return $this->mapCol >= strlen($mapRow);
    }

    public function extendMapRow($mapRow) {
        $this->mapGrower++;
        return str_repeat($mapRow, $this->mapGrower);
    }

    public function isThereATree($mapRow) {
        return self::charAt($mapRow, $this->mapCol) === $this->charTree;
    }
}
