<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

use Illuminate\Support\Str;

class Day7Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->bagToSearch = 'shiny gold bags';
    }

    public function part1() {
        $this->rules = $this->inputRepository->getLuggagesRules();
        $rulesThatContainShinyBag = $this->rules->filter(function($rule) {
            if ($rule->bagContainer === $this->bagToSearch) return false;
            $this->fillMatrixWithAllBagsThatMustContain($rule->bagContainer);

            return $this->bagsMatrix->search(function ($finalRuleSum, $finalRuleKey) {
                return Str::contains($finalRuleKey, $this->bagToSearch);
            });
        });
        return $rulesThatContainShinyBag->count();
    }

    public function part2() {
        $this->rules = $this->inputRepository->getLuggagesRules();
        return 'To Do';
    }

    public function fillMatrixWithAllBagsThatMustContain($bagToFind) {
        $this->bagsToFind = collect([$bagToFind]);
        $this->bagsMatrix = collect([$bagToFind => 0]);

        while (!$this->bagsToFind->isEmpty()) {
            $this->bagsBuffer = collect();
            $this->bagsToForget = collect();

            $this->rules->each(function ($rule) {
                $bagContainer = $rule->bagContainer;
                if ($this->bagsToFind->contains($bagContainer)) {
                    $this->bagsToFind->forget($bagContainer);

                    $rule->bagsContained->each(function ($bagNumber, $bagName) use ($bagContainer) {
                        $keyContainer = $this->bagsMatrix->search(function ($sum, $key) use ($bagContainer) {
                            return Str::endsWith($key, $bagContainer);
                        });
                        $bagNumber += $this->bagsMatrix->get($keyContainer);
                        $newKeyContainer = $keyContainer.'.'.$bagName;

                        $this->bagsMatrix->prepend($bagNumber, $newKeyContainer);
                        $this->bagsToForget->prepend($keyContainer);
                    });
                    $this->bagsBuffer = $this->bagsBuffer->concat($rule->bagsContained->keys());
                }
            });
            $this->bagsMatrix = $this->bagsMatrix->except($this->bagsToForget);
            $this->bagsToFind = $this->bagsBuffer;
            $this->bagsToForget = collect();
        }
    }
}
