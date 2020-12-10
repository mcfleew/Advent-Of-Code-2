<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

use Illuminate\Support\Str;

class Day10Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->diffJoltsMin = 1;
        $this->diffJoltsMax = 3;
        $this->alternatives = collect();
    }

    public function part1() {
        $this->adapters = $this->inputRepository->getJoltageAdapters();
        $this->builtInAdapterRange = $this->getBuiltInAdapter();
        $this->diffCounter = $this->getDifferencesCounter();

        foreach($this->builtInAdapterRange as $builtInAdapter) {
            $joltageAlternatives = $this->getJoltageAlternatives($builtInAdapter);
            $adaptersConcerned = $this->adapters->whereBetween('joltage', $joltageAlternatives);

            $this->countDifferences($builtInAdapter, $adaptersConcerned);
        }
        return $this->diffCounter[$this->diffJoltsMin] * $this->diffCounter[$this->diffJoltsMax];
    }

    public function part2() {
        $this->adapters = $this->inputRepository->getJoltageAdapters();
        $answerPart1 = $this->part1();
        $alternativeWays = $this->countAlternatives();
        return $alternativeWays->count();
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

    public function getJoltageAlternatives($builtInAdapter) {
        return [
            $builtInAdapter['joltage'] + $this->diffJoltsMin,
            $builtInAdapter['joltage'] + $this->diffJoltsMax
        ];
    }

    public function countDifferences($builtInAdapter, $adaptersConcerned) {
        if ($adaptersConcerned->isNotEmpty()) {
            if ($adaptersConcerned->count() > 1) {
                $this->alternatives->push($adaptersConcerned->toArray());
            }
            $adapterConcerned = $adaptersConcerned->shift();
            $diffKey = $adapterConcerned['joltage'] - $builtInAdapter['joltage'];
            $this->diffCounter[$diffKey]++;
        }
    }

    public function countAlternatives() {
        $alternativesWays = collect([':0']);

        foreach($this->alternatives as $alternativesAdapters) {
            $waysToAdd = collect();
            $waysToRem = collect();

            foreach($alternativesAdapters as $alternativeAdapter) {
                $waysConcerned = $alternativesWays->filter(function ($alternativeWay) use ($alternativeAdapter) {
                    return Str::afterLast($alternativeWay,':') < $alternativeAdapter['joltage'];
                });
                
                if ($waysConcerned->isNotEmpty()) {
                    $wayToAdd = $waysConcerned->crossJoin([':'.$alternativeAdapter['joltage']]);
                    $wayToAdd->transform(function($splittedWay) {
                        return implode($splittedWay);
                    });
                    $waysToAdd = $waysToAdd->merge($wayToAdd);
                    $waysToRem = $waysToRem->merge($waysConcerned);
                }
            }

            $alternativesWays = $alternativesWays->merge($waysToAdd);
            $alternativesWays = $alternativesWays->reject(function ($way) use ($waysToRem) {
                return $waysToRem->contains($way);
            });
        }
        return $alternativesWays->sort()->values();
    }
}
