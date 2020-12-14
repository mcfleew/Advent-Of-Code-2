<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

use Illuminate\Support\Str;

class Day14Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->memory = collect();
    }

    public function part1() {
        $this->program = $this->inputRepository->getInitializationProgram();

        $this->program->each(function ($instruction) {
            if (Str::of($instruction)->startsWith('mask')) {
                list($mask) = sscanf($instruction, 'mask = %s');
                $this->updateBitmask($mask);
            }
            if (Str::of($instruction)->startsWith('mem')) {
                list($address, $value) = sscanf($instruction, 'mem[%d] = %d');
                $this->overwriteMemory($address, $value);
            }
        });
        
        return $this->memory->sum();
    }

    public function part2() {
        //
    }

    public function updateBitmask($mask) {
        $this->mask = $mask;
    }

    public function overwriteMemory($address, $value) {
        $zippedMaskAndValue = $this->getZippedMaskAndValue($value);

        $binValueWithMaskApplied = $zippedMaskAndValue->mapSpread(function ($bitFromMask, $bitFromValue) {
            if ($bitFromMask !== 'X') {
                return $bitFromMask;
            }
            return $bitFromValue;
        });

        $valueWithMaskApplied = bindec($binValueWithMaskApplied->implode(''));
        
        $this->memory->put($address, $valueWithMaskApplied);
    }

    public function getZippedMaskAndValue($value) {
        $splittedMask = $this->getSplittedMask();
        $splittedValue = $this->getSplittedValue($value);
        return $splittedMask->zip($splittedValue);
    }

    public function getSplittedMask() {
        return collect(str_split($this->mask));
    }

    public function getSplittedValue($value) {
        $binValue = Str::of(decbin($value))->padLeft(36, '0');
        return collect(str_split($binValue));
    }
}
