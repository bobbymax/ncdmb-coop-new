<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditBudgetHeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $expected_performance = $this->approved_amount != 0 ? ($this->booked_expenditure / $this->approved_amount) * 100 : 0;
        $actual_performance = $this->approved_amount != 0 ? ($this->actual_expenditure / $this->approved_amount) * 100 : 0;

        return [
            'id' => $this->id,
            'description' => $this->description,
            'budgetCode' => $this->subBudgetHead->budgetCode,
            'sub_budget_head_name' => $this->subBudgetHead->name,
            'department' => $this->subBudgetHead->department->code,
            'subBudgetHead' => $this->subBudgetHead,
            'approved_amount' => $this->approved_amount,
            'booked_expenditure' => $this->booked_expenditure,
            'actual_expenditure' => $this->actual_expenditure,
            'booked_balance' => $this->booked_balance,
            'actual_balance' => $this->actual_balance,
            'expected_performance' => $expected_performance,
            'actual_peformance' => $actual_performance,
            'exhausted' => $this->exhausted,
            'budget_year' => (int) $this->budget_year
        ];
    }
}
