<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function init()
    {
        $utilization = $this->getBudgetUtilization();
        $performance = $this->getMonthlyPerformance();
        $summary = $this->getBudgetSummary();

        $data = compact('utilization', 'performance', 'summary');

        return response()->json([
            'data' => $data,
            'status' => 'success',
            'message' => 'Budget Utilization'
        ], 200);
    }

    private function returnBudgetYear()
    {
        return config('site.budget_year') ?? config('budget.budget_year');
    }

    protected function getBudgetUtilization()
    {
        // display: [expenditure, balance]
        // budget controller department
        // fetch total actual expenditure 
        // fetch total actual balance
        // change value to percentage

        $actualExpenditure = 0;
        $actualBalance = 0;
        $budgetYear = config('site.budget_year') ?? config('budget.budget_year');

        foreach (auth()->user()->department->subBudgetHeads as $subBudgetHead) {

            $fund = $subBudgetHead->getCurrentFund($budgetYear);

            if ($fund) {
                $actualExpenditure += $fund->actual_expenditure;
                $actualBalance += $fund->actual_balance;
            }
        }

        return compact('actualExpenditure', 'actualBalance');
    }

    protected function getMonthlyPerformance()
    {
        $actualExpenditure = 0;
        $bookedExpenditure = 0;
        $budgetYear = config('site.budget_year') ?? config('budget.budget_year');
        $performance = [];

        foreach (auth()->user()->department->subBudgetHeads as $subBudgetHead) {

            $fund = $subBudgetHead->getCurrentFund($budgetYear);

            if ($fund) {
                foreach($this->months as $month) {
                    if ($fund->updated_at->format('M') === $month) {
                        $actualExpenditure += $fund->actual_expenditure;
                        $bookedExpenditure += $fund->booked_expenditure;
                        $performance[$fund->updated_at->format('M')] = [$actualExpenditure, $bookedExpenditure];
                    } else {
                        $performance[$month] = [0, 0];
                    }
                } 
            }
        }

        return $performance;
    }

    protected function getBudgetSummary()
    {
        $approvedAmount = 0;
        $bookedExpenditure = 0;
        $actualExpenditure = 0;
        $bookedBalance = 0;
        $actualBalance = 0;
        $expectedPerformance = 0;
        $actualPerformance = 0;

        foreach (auth()->user()->department->subBudgetHeads as $subBudgetHead) {
            $fund = $subBudgetHead->getCurrentFund($this->returnBudgetYear());

            $approvedAmount += $fund->approved_amount;
            $bookedExpenditure += $fund->booked_expenditure;
            $actualExpenditure += $fund->actual_expenditure;
            $bookedBalance += $fund->booked_balance;
            $actualBalance += $fund->actual_balance;
            $expectedPerformance += $fund->approved_amount != 0 ? ($fund->booked_expenditure / $fund->approved_amount) * 100 : 0;
            $actualPerformance += $fund->approved_amount != 0 ? ($fund->actual_expenditure / $fund->approved_amount) * 100 : 0;
        }

        return compact('approvedAmount', 'bookedExpenditure', 'actualExpenditure', 'bookedBalance', 'actualBalance', 'expectedPerformance', 'actualPerformance');
    }
}
