<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubBudgetHeadResource extends JsonResource
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
            'budget_head_id' => $this->budget_head_id,
            'department_id' => $this->department_id,
            'budgetCode' => $this->budgetCode,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'logisticsBudget' => $this->logisticsBudget,
            'department' => $this->department,
            'budgetHead' => $this->budgetHead,
            'fund' => $this->fund,
            'balance' => $this->fund ? $this->fund->actual_balance : 0
        ];
    }
}
