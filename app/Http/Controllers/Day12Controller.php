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
        $this->facingDirection = 'E';
        $this->navigate();
        return $this->getManhattanDistance();
    }

    public function part2() {
        $this->instructions = $this->inputRepository->getNavigationInstructions();
        $this->waypoint = collect($this->map)->merge(['N' => 1, 'E' => 10]);
        $this->navigate(false);
        return $this->getManhattanDistance();
    }

    public function navigate($part1 = true) {
        foreach ($this->instructions as $instruction) {
            $action = $instruction->action;
            $units = $instruction->units;

            if (in_array($action, ['F'])) {
                if ($part1) $this->moveToward($this->facingDirection, $units);
                if (!$part1) $this->moveWaypointForward($units);
            }
            if (in_array($action, array_keys($this->map))) { // N, S, E, W;
                if ($part1) $this->moveToward($action, $units);
                if (!$part1) $this->moveWaypointToward($action, $units);
            }
            if (in_array($action, array_keys($this->rotation))) { // L, R;
                if ($part1) $this->facingDirection = $this->getNewDirection($action, $this->facingDirection, $units);
                if (!$part1) $this->changeWaypointDirection($action, $units);
            }
        }
    }

    public function moveToward($direction, $units) {
        $this->map[$direction] += $units;
    }

    public function getNewDirection($action, $direction, $degrees) {
        return $this->rotation[$action][$direction][$degrees];
    }

    public function moveWaypointForward($times) {
        $this->waypoint->each(function ($units, $direction) use ($times) {
            $this->map[$direction] += $units * $times;
        });
    }

    public function moveWaypointToward($direction, $units) {
        $this->waypoint[$direction] += $units;
    }

    public function changeWaypointDirection($action, $degrees) {
        $this->waypoint = $this->waypoint->mapWithKeys(function ($units, $direction) use ($action, $degrees) {
            $newDirection = $this->getNewDirection($action, $direction, $degrees);
            return collect([$newDirection => $units]);
        });
    }

    public function getManhattanDistance() {
        return abs($this->map['N'] - $this->map['S']) + abs($this->map['E'] - $this->map['W']);
    } 
}
