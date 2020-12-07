<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day6Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
    }

    public function part1() {
        $this->forms = $this->inputRepository->getDeclarationForms();
        $answers = $this->forms->map(function($answersGroups) {
            return $this->getQuestionsAnyoneReplied($answersGroups);
        });
        return $answers->sum();
    }

    public function part2() {
        $this->forms = $this->inputRepository->getDeclarationForms();
        $answers = $this->forms->map(function($answersGroups) {
            return $this->getQuestionsEveryoneReplied($answersGroups);
        });
        return $answers->sum();
    }

    public function getQuestionsAnyoneReplied($answersGroups) {
        $concat = $answersGroups->shift();
        foreach($answersGroups as $answers) {
            $concat = $concat->concat($answers);
        }
        return $concat->unique()->count();
    }

    public function getQuestionsEveryoneReplied($answersGroups) {
        $intersect = $answersGroups->shift();
        foreach($answersGroups as $answers) {
            $intersect = $intersect->intersect($answers);
        }
        return $intersect->count();
    }
}
