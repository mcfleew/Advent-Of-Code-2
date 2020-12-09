<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

use Illuminate\Support\Str;

class Day4Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->requiredFields = [
            'byr', // (Birth Year)
            'iyr', // (Issue Year)
            'eyr', // (Expiration Year)
            'hgt', // (Height)
            'hcl', // (Hair Color)
            'ecl', // (Eye Color)
            'pid', // (Passport ID)
            // 'cid', (Country ID)
        ];
    }
    
    public function part1() {
        $passports = $this->inputRepository->getPassportsData();
        
        $validPassports = $passports->filter(function ($passportData) {
            $data = $this->getPassportDataExceptCid($passportData);
            return $data->has($this->requiredFields);
        });

        return $validPassports->count();
    }

    public function part2() {
        $passports = $this->inputRepository->getPassportsData();
        
        $validPassports = $passports->filter(function ($passportData) {
            $data = $this->getPassportDataExceptCid($passportData);
            if ($data->has($this->requiredFields)) {
                return $this->isAllFiledsValid($data); 
            }
        });

        return $validPassports->count();
    }

    public function getPassportDataExceptCid($passportData) {
        return $passportData->mapWithKeys(function($fieldData) {
            return collect($fieldData)->except('cid');
        });
    }

    public function isAllFiledsValid($data) {
        return $data->every(function ($fieldValue, $fieldKey) {
            return $this->isThisFieldValid($fieldKey, $fieldValue);
        });
    }

    public function isThisFieldValid($fieldKey, $fieldValue) {
        switch ($fieldKey) {
            case 'byr':
                return (1920 <= $fieldValue && $fieldValue <= 2002);
                break;
            case 'iyr':
                return (2010 <= $fieldValue && $fieldValue <= 2020);
                break;
            case 'eyr':
                return (2020 <= $fieldValue && $fieldValue <= 2030);
                break;
            case 'hgt':
                $rightCmSize = $rightInSize = false;
                $matches = Str::of($fieldValue)->matchAll('/(^([0-9]{2,3})|(cm|in)$)/');
                if ($matches->count() === 2) {
                    list($size, $metric) = $matches;
                    $rightCmSize = ($metric == 'cm' && (150 <= $size && $size <= 193));
                    $rightInSize = ($metric == 'in' && (59 <= $size && $size <= 76));
                }
                return $rightCmSize || $rightInSize;
                break;
            case 'hcl':
                return Str::of($fieldValue)->match('/^#[0-9a-f]{6}$/')->isNotEmpty();
                break;
            case 'ecl':
                return Str::of($fieldValue)->match('/^(amb|blu|brn|gry|grn|hzl|oth)$/')->isNotEmpty();
                break;
            case 'pid':
                return Str::of($fieldValue)->match('/^[0-9]{9}$/')->isNotEmpty();
                break;
        }
    }
}
