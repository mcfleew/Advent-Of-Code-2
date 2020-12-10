<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day10Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->diffJoltsMin = 1;
        $this->diffJoltsMax = 3;
    }

    public function part1() {
        $this->adapters = $this->inputRepository->getJoltageAdapters();
        $this->builtInAdapterRange = $this->getBuiltInAdapter();
        $this->diffCounter = $this->getDifferencesCounter();

        foreach($this->builtInAdapterRange as $builtInAdapter) {
            $joltagePossibilities = $this->getJoltagePossibilities($builtInAdapter);
            $adaptersConcerned = $this->adapters->whereBetween('joltage', $joltagePossibilities);

            $this->countDifferences($builtInAdapter, $adaptersConcerned);
        }
        return $this->diffCounter[$this->diffJoltsMin] * $this->diffCounter[$this->diffJoltsMax];
    }

    public function part2() {
        $this->adapters = $this->inputRepository->getJoltageAdapters();
    }

    public function getBuiltInAdapter() {
        $biAdapter = $this->adapters;
        $biAdapter->prepend(['joltage' => 0]);
        $biAdapter->push(['joltage' => $this->adapters->max('joltage') + 3]);
        return $biAdapter;
    }

    public function getDifferencesCounter() {
        return array_fill($this->diffJoltsMin, $this->diffJoltsMax, 0);
    }

    public function getJoltagePossibilities($builtInAdapter) {
        return [
            $builtInAdapter['joltage'] + $this->diffJoltsMin,
            $builtInAdapter['joltage'] + $this->diffJoltsMax
        ];
    }

    public function countDifferences($builtInAdapter, $adaptersConcerned) {
        if ($adaptersConcerned->isNotEmpty()) {
            $adapterConcerned = $adaptersConcerned->shift();
            $diffKey = $adapterConcerned['joltage'] - $builtInAdapter['joltage'];
            $this->diffCounter[$diffKey]++;
        }
    }
}
