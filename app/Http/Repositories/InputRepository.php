<?php

namespace App\Http\Repositories;

use App\Http\Traits\DebugTrait;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InputRepository
{
    use DebugTrait;

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
        $passportsData = $this->getInputCollectionSplitBy('day4.txt');

        return $passportsData->map(function ($passportDataRaw) {
            $passportData = Str::of($passportDataRaw)->split('/[\s\n]')->filter();

            return $passportData->map(function ($data) {
                list($field, $value) = Str::of($data)->explode(':');
                return [$field => $value];
            });
        });
    }

    public function getBoardingPasses() {
        return $this->getInputCollection('day5.txt');
    }

    public function getDeclarationForms() {
        $declarationForms = $this->getInputCollectionSplitBy('day6.txt');

        return $declarationForms->map(function ($forms) {
            return Str::of($forms)->explode("\n")->map(function ($answers) {
                return collect(str_split($answers));
            });
        });
    }

    public function getLuggagesRules() {
        $luggagesRules = $this->getInputCollection('day7.txt');

        return $luggagesRules->map(function ($luggageRule) {
            $bagContainer = Str::before($luggageRule, ' contain ');
            $bagContent = Str::after($luggageRule, ' contain ');
            $bagsContainedRaw = Str::of($bagContent)->matchAll('/([0-9]+ [a-z-A-Z ]+)[\.,]/')->filter();

            $bagsContained = $bagsContainedRaw->mapWithKeys(function ($bagContained) {
                return collect([
                    Str::finish(Str::substr($bagContained, 2), 's') => 
                    Str::substr($bagContained, 0, 1)
                ]);
            });

            return (object) [
                'bagContainer' => $bagContainer,
                'bagsContained' => $bagsContained
            ];
        });
    }

    public function getInputCollection($file) {
        return collect(file($this->getInputFile($file), FILE_IGNORE_NEW_LINES));
    }

    public function getInputCollectionSplitBy($file, $delimeter = '\n\n') {
        return Str::of($this->getInputContents($file))->explode($delimeter);
    }

    public function getInputContents($file) {
        return file_get_contents($this->getInputFile($file));
    }

    public function getInputFile($file) {
        return resource_path($this->inputPath.$file);
    }
}