<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day2Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->passwords = $this->inputRepository->getPasswordsList();

        $validPasswords = $this->passwords->filter(function ($password) {
            return $this->isIntancesLetterCountValid($password);
        });

        return $validPasswords->count();
    }

    public function part2() {
        $this->passwords = $this->inputRepository->getPasswordsList();

        $validPasswords = $this->passwords->filter(function ($password) {
            return $this->isOnePositionLetterValid($password);
        });

        return $validPasswords->count();
    }

    public function isIntancesLetterCountValid($p) {
        $lettersInstances = self::getCharInstances($p->password);

        if ($lettersInstances->has($p->letter)) {
            $letterInstancesCount = $lettersInstances->get($p->letter);

            return $p->minValue <= $letterInstancesCount && $letterInstancesCount <= $p->maxValue;
        }
        return false;
    }

    public function isOnePositionLetterValid($p) {
        $firstPositionValid = $p->letter === self::charAt($p->password, $p->minValue - 1);
        $lastPositionValid = $p->letter === self::charAt($p->password, $p->maxValue - 1);
        return $firstPositionValid xor $lastPositionValid;
    }
}
