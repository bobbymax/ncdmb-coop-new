<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubBudgetHead;
use App\Models\Department;
use App\Http\Resources\SubBudgetHeadResource;

class BudgetSummaryController extends Controller
{

    protected $totalApprovedAmount, $totalBookedExpenditure, $totalActualExpenditure, $totalBookedBalance, $totalActualBalance, $expectedPerformance, $actualPerformance;

    protected $monthsOfTheYear = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getBudgetOverview()
    {
        // 1. Get Authenticated user and department
        $department = auth()->user()->departments->where('type', 'department')->first();
        // 2. fetch all sub budgets heads for that department
        $subBudgetHeads = $department->subBudgetHeads;
        // 3. Sum all parameters in the fetched sub budget heads
        $currentYear = config('site.budget_year') ?? config('budget.budget_year');
        
        foreach ($subBudgetHeads as $subBudgetHead) {
            $fund = $subBudgetHead->getCurrentFund($currentYear);

            $expPerf = $fund->approved_amount != 0 ? ($fund->booked_expenditure / $fund->approved_amount) * 100 : 0;
            $actPerf = $fund->approved_amount != 0 ? ($fund->actual_expenditure / $fund->approved_amount) * 100 : 0;

            $this->totalApprovedAmount += $fund->approved_amount;
            $this->totalBookedExpenditure += $fund->booked_expenditure;
            $this->totalActualExpenditure += $fund->actual_expenditure;
            $this->totalBookedBalance += $fund->booked_balance;
            $this->totalActualBalance += $fund->actual_balance;
            $this->expectedPerformance += $expPerf;
            $this->actualPerformance += $actPerf;
        }
        // 4. construct an aray of values
        $approved = $this->totalApprovedAmount;
        $bookedExpenditure = $this->totalBookedExpenditure;
        $actualExpenditure = $this->totalActualExpenditure;
        $bookedBalance = $this->totalBookedBalance;
        $actualBalance = $this->totalActualBalance;
        $expectedPerf = round($this->expectedPerformance, 2);
        $actualPerf = round($this->actualPerformance, 2);
        $budgetHeads = SubBudgetHeadResource::collection($subBudgetHeads);


        $data = compact('approved', 'bookedExpenditure', 'actualExpenditure', 'bookedBalance', 'actualBalance', 'expectedPerf', 'actualPerf', 'budgetHeads');


        // 5. send back to the front end
        return response()->json([
            'data' => $data,
            'status' => 'success',
            'message' => 'Budget Summary'
        ], 200);
    }

    public function chartDisplay(Request $request)
    {
        $month = "";
        $sumTotalExpected = 0;
        $sumTotalActual = 0;
        // 1. Get Authenticated user and department
        $department = auth()->user()->departments->where('type', 'department')->first();
        // 2. fetch all sub budgets heads for that department
        $subBudgetHeads = $department->subBudgetHeads;
        // 3. Sum all parameters in the fetched sub budget heads
        $currentYear = config('site.budget_year') ?? config('budget.budget_year');
        if ($request->report === "month") {
            $month = $this->monthsOfTheYear[now()->month - 1];

            foreach($subBudgetHeads as $subBudgetHead) {
                $fund = $subBudgetHead->getCurrentFund($currentYear);

                if ($month === $fund->updated_at->format('F')) {
                    $sumTotalExpected += $fund->booked_expenditure;
                    $sumTotalActual += $fund->actual_expenditure;
                }
            }

        }
        $months = [$month];
        $exp = [$sumTotalExpected];
        $act = [$sumTotalActual];

        $data = compact('months', 'exp', 'act');

        return response()->json([
            'data' => $data,
            'status' => 'success',
            'message' => 'EXPECTED'
        ], 200);
    }

    public function getBudgetSummary($department)
    {
        $dept = Department::find($department);

        if (! $dept) {
            return response()->json([
                'data' => null,
                'status' => 'error',
                'message' => 'Invalid department ID'
            ], 422);
        }

        return response()->json([
            'data' => SubBudgetHeadResource::collection($dept->subBudgetHeads),
            'status' => 'success',
            'message' => 'Budget Summary for Department'
        ], 200);
    }
}
