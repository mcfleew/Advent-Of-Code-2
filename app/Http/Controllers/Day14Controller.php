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

        $this->runProgram();

        return $this->memory->sum();
    }

    public function part2() {
        $this->program = $this->inputRepository->getInitializationProgram();

        $this->runProgram(false);
        
        return $this->memory->sum();
    }

    public function runProgram($part1 = true) {
        $this->program->each(function ($instruction) use ($part1) {
            if (Str::of($instruction)->startsWith('mask')) {
                list($mask) = sscanf($instruction, 'mask = %s');
                $this->updateBitmask($mask);
            }
            if (Str::of($instruction)->startsWith('mem')) {
                list($address, $value) = sscanf($instruction, 'mem[%d] = %d');
                if ($part1) $this->overwriteMemory($address, $value);
                if (!$part1) $this->overwriteMemoryV2($address, $value);
            }
        });
    }

    public function updateBitmask($mask) {
        $this->mask = $mask;
    }

    public function overwriteMemory($address, $value) {
        $zippedMaskAndValue = $this->getZippedMaskAndValueOrAddress($value);

        $binValueWithMaskApplied = $zippedMaskAndValue->mapSpread(function ($bitFromMask, $bitFromValue) {
            if ($bitFromMask === 'X') {
                return $bitFromValue;
            } else {
                return $bitFromMask;
            }
        });

        $valueWithMaskApplied = bindec($binValueWithMaskApplied->implode(''));
        
        $this->memory->put($address, $valueWithMaskApplied);
    }

    public function overwriteMemoryV2($address, $value) {
        $zippedMaskAndAddress = $this->getZippedMaskAndValueOrAddress($address);

        $this->binAddressesWithMaskApplied = collect(['']);
        
        $zippedMaskAndAddress->eachSpread(function ($bitFromMask, $bitFromAddress) {
            $buffer = $this->binAddressesWithMaskApplied;

            if ($bitFromMask === 'X') {
                $buffer = $buffer->crossJoin([':0', ':1']);
            } else if ($bitFromMask === '1') {
                $buffer = $buffer->crossJoin([':1']);
            } else if ($bitFromMask === '0') {
                $buffer = $buffer->crossJoin([':'.$bitFromAddress]);
            }
            
            $this->binAddressesWithMaskApplied = $buffer->map(function ($bitsWithMaskApplied) {
                return collect($bitsWithMaskApplied)->implode('');
            });
        });

        $this->binAddressesWithMaskApplied->each(function ($addressBitsWithMaskApplied) use ($value) {
            $addressWithMaskApplied = bindec(Str::of($addressBitsWithMaskApplied)->explode(':')->implode(''));
            $this->memory->put($addressWithMaskApplied, $value);
        });
    }

    public function getZippedMaskAndValueOrAddress($valueOrAddress) {
        $splittedMask = $this->getSplittedMask();
        $splittedValueOrAddress = $this->getSplittedValueOrAddress($valueOrAddress);
        return $splittedMask->zip($splittedValueOrAddress);
    }

    public function getSplittedMask() {
        return collect(str_split($this->mask));
    }

    public function getSplittedValueOrAddress($valueOrAdress) {
        $binValueOrAdress = Str::of(decbin($valueOrAdress))->padLeft(36, '0');
        return collect(str_split($binValueOrAdress));
    }
}
