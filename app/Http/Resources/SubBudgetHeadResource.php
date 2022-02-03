<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubBudgetHeadResource extends JsonResource
{

    protected $approvedAmount, $bookedExpenditure, $actualExpenditure, $bookedBalance, $actualBalance, $exp, $act;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $year = 2021;
        $fund = $this->getCurrentFund($year);

        if ($fund) {
            $this->exp = $fund->approved_amount > 0 ? ($fund->booked_expenditure / $fund->approved_amount) * 100 : 0;
            $this->act = $fund->approved_amount > 0 ? ($fund->actual_expenditure / $fund->approved_amount) * 100 : 0;
        }
        
        return [
            'id' => $this->id,
            'budget_head_id' => $this->budget_head_id,
            'budget_head' => $this->budgetHead->budgetId,
            'department_code' => $this->department->code,
            'department_id' => $this->department_id,
            'budgetCode' => $this->budgetCode,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'logisticsBudget' => $this->logisticsBudget ? 'Yes' : 'No',
            'department' => $this->department,
            'budgetHead' => $this->budgetHead,
            'fund' => new CreditBudgetHeadResource($this->getCurrentFund($year)),
            'funds' => $this->funds,
            'approved_amount' => $fund->approved_amount,
            'booked_expenditure' => $fund->booked_expenditure,
            'actual_expenditure' => $fund->actual_expenditure,
            'booked_balance' => $fund->booked_balance,
            'actual_balance' => $fund->actual_balance,
            'expected_performance' => $fund && $this->exp ? round($this->exp) . '%' : 0,
            'actual_performance' => $fund && $this->act ? round($this->act) . '%' : 0,
            'totals' => $this->getTotals()
        ];
    }

    public function getTotals()
    {
        $currentYear = 2021;

        foreach($this->department->subBudgetHeads as $subBudgetHead)
        {
            $fund = $subBudgetHead->getCurrentFund($currentYear);

            if ($fund) {
                $this->approvedAmount += $fund->approved_amount;
                $this->bookedExpenditure += $fund->booked_expenditure;
                $this->actualExpenditure += $fund->actual_expenditure;
                $this->bookedBalance += $fund->booked_balance;
                $this->actualBalance += $fund->actual_balance;
            }
        }

        $appAmount = $this->approvedAmount ?? 0;
        $bookExp = $this->bookedExpenditure ?? 0;
        $actExp = $this->actualExpenditure ?? 0;
        $bookBal = $this->bookedBalance ?? 0;
        $actBal = $this->actualBalance ?? 0;

        return compact('appAmount', 'bookExp', 'actExp', 'bookBal', 'actBal');
    }
}
