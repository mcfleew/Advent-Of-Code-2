<?php

namespace App\Http\Controllers;

use App\Http\Repositories\InputRepository;

class Day1Controller extends Controller
{
    public function __construct(InputRepository $inputRepository) {
        $this->inputRepository = $inputRepository;
        $this->target = 2020;
    }

    public function part1() {
        $this->expenses = $this->inputRepository->getExpenseReport();

        foreach($this->expenses as $expense) {
            $expensesMatrix = $this->createSimpleComparisonMatrix($expense);

            if ($expensesEqualsTarget = $this->getFirstMatrixWhereSumEqualsTarget($expensesMatrix)) {
                return $this->multiplyExpensesTogether($expensesEqualsTarget);
            }
        }
    }

    public function part2() {
        $this->expenses = $this->inputRepository->getExpenseReport();

        foreach($this->expenses as $expense) {
            $expensesMatrix = $this->createDoubleComparisonMatrix($expense);

            if ($expensesEqualsTarget = $this->getFirstMatrixWhereSumEqualsTarget($expensesMatrix)) {
                return $this->multiplyExpensesTogether($expensesEqualsTarget); 
            }
        }
    }

    public function createSimpleComparisonMatrix($expense) {
        return $this->expenses->crossJoin([$expense]);
    }

    public function createDoubleComparisonMatrix($expense) {
        return $this->expenses->crossJoin($this->expenses, [$expense]);
    }

    public function getFirstMatrixWhereSumEqualsTarget($expensesMatrix) {
        return $expensesMatrix->first(function ($matrix, $index) {
            return array_sum($matrix) === $this->target;
        });
    }

    public function multiplyExpensesTogether($expenses) {
        return self::array_product($expenses); 
    }
}
