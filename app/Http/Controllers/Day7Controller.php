<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day7Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->bagGoldAndShiny = 'shiny gold bags';
    }

    public function part1() {
        $this->rules = $this->inputRepository->getLuggagesRules();
        $rulesThatContainShinyBag = $this->rules->filter(function($rule) {
            if ($rule->bagContainer === $this->bagGoldAndShiny) return false;
            $this->getTraceOfBagsContainedBy($rule->bagContainer);

            return $this->bagsTrace->first(function ($bagContainer) {
                return $bagContainer === $this->bagGoldAndShiny;
            });
        });
        return $rulesThatContainShinyBag->count();
    }

    public function part2() {
        $this->rules = $this->inputRepository->getLuggagesRules();
        return $this->getSumOfBagsContainedBy($this->bagGoldAndShiny);
    }

    public function getBagsContainedBy($bagToFind, $traceGoldAndShiny = true) {
        $this->bagsToFind = collect([$bagToFind]);
        $this->bagsBuffer = collect();

        while (!$this->bagsToFind->isEmpty() && !($traceGoldAndShiny
            && $this->bagsToFind->contains($this->bagGoldAndShiny))) {
            $this->bagsToFind->each(function ($bagToFind) {
                $rule = $this->rules->first(function ($rule) use ($bagToFind) {
                    return $rule->bagContainer === $bagToFind;
                });

                $this->bagsBuffer = $this->bagsBuffer->concat($rule->bagsContained->keys());

                if (isset($this->bagsTrace)) $this->traceAllBagsInsideBigBag($bagToFind, $rule);
                if (isset($this->calcString)) $this->countAllBagsInsideBigBag($bagToFind, $rule);
            });
            $this->bagsToFind = $this->bagsBuffer;
            $this->bagsBuffer = collect();
        }
    }

    public function traceAllBagsInsideBigBag($bagToFind, $rule) {
        if ($rule->bagsContained->has($this->bagGoldAndShiny)) {
            $this->bagsTrace->prepend($this->bagGoldAndShiny);
        }
    }

    public function countAllBagsInsideBigBag($bagToFind, $rule) {
        if (!$rule->bagsContained->isEmpty()) {
            $this->calcString = preg_replace_array('/'.$bagToFind.':'.$bagToFind.'/', array_fill(0, 10,
                $bagToFind.':'.$rule->bagsContained->keys()->implode(' + '.$bagToFind.':')), $this->calcString);
                
            $rule->bagsContained->each(function ($bagCount, $bagContained) use ($bagToFind) {
                $this->calcString = preg_replace_array('/'.$bagToFind.':'.$bagContained.'/', array_fill(0, 10,
                '('.$bagCount.' + '.$bagCount.' * ('.$bagContained.':'.$bagContained.'))'), $this->calcString);
            });
        } else {                    
            $this->calcString = preg_replace_array(
                '/\s\+\s\d\s\*\s\(('.$bagToFind.':'.$bagToFind.'|\s\+\s)\)/',
                array_fill(0, 10, ''), $this->calcString);
        }
    }

    public function getTraceOfBagsContainedBy($bagToFind) {
        $this->bagsTrace = collect([$bagToFind]);
        $this->getBagsContainedBy($bagToFind);
    }

    public function getSumOfBagsContainedBy($bagToFind) {
        $this->calcString = $bagToFind.':'.$bagToFind;
        $this->getBagsContainedBy($bagToFind, false);

        $calc = $this->calcString;
        eval('$calc = '.$calc.';'); 
        return $calc;
    }
}
