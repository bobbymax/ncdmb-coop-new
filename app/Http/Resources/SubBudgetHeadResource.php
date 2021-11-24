<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubBudgetHeadResource extends JsonResource
{

    protected $approvedAmount, $bookedExpenditure, $actualExpenditure, $bookedBalance, $actualBalance;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $year = date('Y');
        $fund = $this->getCurrentFund($year);
        $exp = $fund->approved_amount != 0 ? ($fund->booked_expenditure / $fund->approved_amount) * 100 : 0;
        $act = $fund->approved_amount != 0 ? ($fund->actual_expenditure / $fund->approved_amount) * 100 : 0;
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'budget_head_id' => $this->budget_head_id,
            'department_id' => $this->department_id,
            'budgetCode' => $this->budgetCode,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'logisticsBudget' => $this->logisticsBudget,
            'department' => $this->department,
            'budgetHead' => $this->budgetHead,
            'fund' => new CreditBudgetHeadResource($this->getCurrentFund($year)),
            'funds' => $this->funds,
            'balance' => $this->fund ? $this->fund->actual_balance : 0,
            'approved_amount' => $fund->approved_amount,
            'booked_expenditure' => $fund->booked_expenditure,
            'actual_expenditure' => $fund->actual_expenditure,
            'booked_balance' => $fund->booked_balance,
            'actual_balance' => $fund->actual_balance,
            'expected_performance' => round($exp) . '%',
            'actual_performance' => round($act) . '%',
            'totals' => $this->getTotals()
        ];
    }

    public function getTotals()
    {
        $currentYear = date('Y');
        foreach($this->department->subBudgetHeads as $subBudgetHead)
        {
            $fund = $subBudgetHead->getCurrentFund($currentYear);
            $this->approvedAmount += $fund->approved_amount;
            $this->bookedExpenditure += $fund->booked_expenditure;
            $this->actualExpenditure += $fund->actual_expenditure;
            $this->bookedBalance += $fund->booked_balance;
            $this->actualBalance += $fund->actual_balance;
        }

        $appAmount = $this->approvedAmount;
        $bookExp = $this->bookedExpenditure;
        $actExp = $this->actualExpenditure;
        $bookBal = $this->bookedBalance;
        $actBal = $this->actualBalance;

        return compact('appAmount', 'bookExp', 'actExp', 'bookBal', 'actBal');
    }
}
