<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day8Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->code = $this->inputRepository->getBootCode();
        $this->initGlobal();
        $this->runCode();

        return $this->accumulator;
    }

    public function part2() {
        $this->code = $this->inputRepository->getBootCode();
        $this->replaced = collect();

        do {
            $this->initGlobal();
            $this->underTest = null;
            $this->runCode(true);
        } while ($this->codeFails());

        return $this->accumulator;
    }

    public function initGlobal() {
        $this->counter = 0;
        $this->accumulator = 0;
        $this->visited = collect();
        $this->codeBreak = false;
    }

    public function runCode($repareCode = false) {
        while ($this->counter < $this->code->count()) {
            if (!$this->visited->has($this->counter)) {
                $instruction = $this->code->get($this->counter);
                $operation = $instruction->operation;
                $argument = $instruction->argument;

                $this->visited->put($this->counter, $instruction);

                if ($repareCode) {
                    $operation = $this->changeOperation($operation, $argument);
                }
                $this->executeInstruction($operation, $argument);
            } else {
                $this->codeBreak = true;
                break;
            }
        }
        return $this->accumulator;
    }

    public function executeInstruction($operation, $argument) {
        $this->counter += ($operation === 'jmp') ? $argument : 1;
        $this->counter += ($operation === 'acc') ? $argument : 0;
    }

    public function changeOperation($operation, $argument) {
        if (in_array($operation, ['nop', 'jmp'])) {
            if (is_null($this->underTest) && !$this->replaced->contains($this->counter)) {
                if ($argument !== 0) {
                    $operation = ('jnp') ? 'nop' : 'jnp';
                }
                $this->underTest = $this->counter;
                $this->replaced->push($this->counter);
            }
        }
        return $operation;
    }

    public function codeFails() {
        return $this->accumulator < 0 || $this->codeBreak;
    }
}
