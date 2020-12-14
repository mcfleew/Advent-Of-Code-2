<?php

namespace App\Jobs;

use Log;

use Illuminate\Support\Str;

class AlternativesCounterJob extends Job
{
    
    protected $alternatives;

    public function __construct($alternatives)
    {
        $this->alternatives = $alternatives;
    }

    public function handle()
    {
        $alternativesWays = $this->countAlternatives();
        Log::info('Alternatives count : '.$alternativesWays->count());
    }

    public function countAlternatives() {
        $alternativesWays = collect([':0']);

        foreach($this->alternatives as $alternativesPhases => $alternativesAdapters) {
            $waysToAdd = collect();
            $waysToRem = collect();

            foreach($alternativesAdapters as $alternativeAdapter) {
                $waysConcerned = $alternativesWays->filter(function ($alternativeWay) use ($alternativeAdapter) {
                    return Str::afterLast($alternativeWay,':') < $alternativeAdapter['joltage'];
                });
                
                if ($waysConcerned->isNotEmpty()) {
                    $wayToAdd = $waysConcerned->map(function ($partialWay) use ($alternativeAdapter, $alternativesPhases) {
                        $completeWay = $partialWay.':'.$alternativeAdapter['joltage'];
                        return ($alternativesPhases < 2) ? $completeWay : Str::after($completeWay, ':');
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
