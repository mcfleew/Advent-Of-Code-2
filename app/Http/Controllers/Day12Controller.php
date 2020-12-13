<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day12Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->map = ['N' => 0, 'S' => 0, 'E' => 0, 'W' => 0];
        $this->rotation = [
            'L' => [
                'N' => [90 => 'W', 180 => 'S', 270 => 'E'],
                'S' => [90 => 'E', 180 => 'N', 270 => 'W'],
                'E' => [90 => 'N', 180 => 'W', 270 => 'S'],
                'W' => [90 => 'S', 180 => 'E', 270 => 'N']
            ],
            'R' => [
                'N' => [90 => 'E', 180 => 'S', 270 => 'W'],
                'S' => [90 => 'W', 180 => 'N', 270 => 'E'],
                'E' => [90 => 'S', 180 => 'W', 270 => 'N'],
                'W' => [90 => 'N', 180 => 'E', 270 => 'S']
            ]
        ];
    }

    public function part1() {
        $this->instructions = $this->inputRepository->getNavigationInstructions();
        $this->navigate();
        return abs($this->map['N'] - $this->map['S']) + abs($this->map['E'] - $this->map['W']);
    }

    public function part2() {
        $this->instructions = $this->inputRepository->getNavigationInstructions();
    }

    public function navigate() {
        $facingDirection = 'E';

        foreach ($this->instructions as $instruction) {
            $direction = $instruction->action;
            $units = $instruction->units;

            if (in_array($direction, ['F'])) {
                $this->moveToward($facingDirection, $units);
            }
            if (in_array($direction, array_keys($this->map))) { // N, S, E, W;
                $this->moveToward($direction, $units);
            }
            if (in_array($direction, array_keys($this->rotation))) { // L, R;
                $facingDirection = $this->changeFacingDirection($facingDirection, $direction, $units);
            }
            // $oppositeAction = current($rotation)[$direction][180];
        }
    }

    public function moveToward($direction, $units) {
        $this->map[$direction] += $units;
    }

    public function changeFacingDirection($facingDirection, $direction, $units) {
        return $this->rotation[$direction][$facingDirection][$units];
    }
}
