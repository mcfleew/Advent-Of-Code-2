<?php

namespace App\Http\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InputRepository
{
    protected $inputPath;

    public function __construct()
    {
        $this->inputPath = 'inputs/';
    }

    public function getExpenseReport() {
        return $this->getInputCollection('day1.txt');
    }

    public function getPasswordsList() {
        $policiesAndPasswords = $this->getInputCollection('day2.txt');

        return $policiesAndPasswords->map(function ($passwordAndPoicy) {
            list($policy, $password) = explode(': ', $passwordAndPoicy);
            list($minValue, $maxValue, $letter) = sscanf($policy, '%d-%d %s');

            return (object) [
                'letter' => $letter,
                'minValue' => $minValue,
                'maxValue' => $maxValue,
                'password' => $password
            ];
        });
    }

    public function getOpenSquaresAndTrees() {
        return $this->getInputCollection('day3.txt');
    }

    public function getPassportsData() {
        $passportsData = $this->getInputContents('day4.txt');
        $passportsDataCollection = Str::of($passportsData)->explode("\n\n");

        return $passportsDataCollection->map(function ($passportDataRaw, $key) {
            $passportData = Str::of($passportDataRaw)->split('/\s|\n/');

            return $passportData->map(function ($data, $key) {
                if ($data) {
                    list($field, $value) = Str::of($data)->explode(':');
                    return [$field => $value];
                }
            });
        });
    }

    public function getBoardingPasses() {
        return $this->getInputCollection('day5.txt');
    }

    public function getDeclarationForms() {
        $declarationForms = $this->getInputContents('day6.txt');
        $declarationFormsCollection = Str::of($declarationForms)->explode("\n\n");

        return $declarationFormsCollection->map(function ($forms) {
            return Str::of($forms)->explode("\n")->map(function ($answers) {
                return collect(str_split($answers));
            });
        });
    }

    public function getInputCollection($file) {
        return collect(file($this->getInputFile($file), FILE_IGNORE_NEW_LINES));
    }

    public function getInputContents($file) {
        return file_get_contents($this->getInputFile($file));
    }

    public function getInputFile($file) {
        return resource_path($this->inputPath.$file);
    }
}
