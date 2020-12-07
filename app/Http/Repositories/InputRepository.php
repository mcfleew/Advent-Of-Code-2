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
        $expenses = $this->getInputAsArray('day1.txt');
        return collect($expenses);
    }

    public function getPasswordsList() {
        $policiesAndPasswords = $this->getInputAsArray('day2.txt');
        $policiesAndPasswordsCollection = collect($policiesAndPasswords);

        $policiesAndPasswordsCollection->transform(function ($passwordAndPoicy, $key) {
            list($policy, $password) = explode(': ', $passwordAndPoicy);
            list($minValue, $maxValue, $letter) = sscanf($policy, '%d-%d %s');

            return (object) [
                'letter' => $letter,
                'minValue' => $minValue,
                'maxValue' => $maxValue,
                'password' => $password
            ];
        });
        return $policiesAndPasswordsCollection;
    }

    public function getOpenSquaresAndTrees() {
        $openSquaresAndTrees = $this->getInputAsArray('day3.txt');
        return collect($openSquaresAndTrees);
    }

    public function getPassportsData() {
        $passportsData = $this->getInputContents('day4.txt');
        $passportsDataCollection = Str::of($passportsData)->explode("\n\n");

        $passportsDataCollection->transform(function ($passportDataRaw, $key) {
            $passportData = Str::of($passportDataRaw)->split('/\s|\n/');

            return $passportData->map(function ($data, $key) {
                if ($data) {
                    list($field, $value) = Str::of($data)->explode(':');
                    return [$field => $value];
                }
            });
        });
        return collect($passportsDataCollection);
    }

    public function getInputAsArray($file) {
        return file($this->getInputFile($file), FILE_IGNORE_NEW_LINES);
    }

    public function getInputContents($file) {
        return file_get_contents($this->getInputFile($file));
    }

    public function getInputFile($file) {
        return resource_path($this->inputPath.$file);
    }
}
