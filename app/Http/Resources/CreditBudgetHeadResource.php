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
        return [
            'id' => $this->id,
            'description' => $this->description,
            'subBudgetHead' => new SubBudgetHeadResource($this->subBudgetHead),
            'approved_amount' => $this->approved_amount,
            'booked_expenditure' => $this->booked_expenditure,
            'actual_expenditure' => $this->actual_expenditure,
            'booked_balance' => $this->booked_balance,
            'actual_balance' => $this->actual_balance,
            'expected_performance' => $this->expected_performance,
            'actual_peformance' => $this->actual_performance,
            'exhausted' => $this->exhausted,
            'budget_year' => $this->budget_year
        ];
    }
}
