<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RefundResource extends JsonResource
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
            'expenditure_id' => $this->expenditure_id,
            'department_id' => $this->department_id,
            'user_id' => $this->user_id,
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'description' => $this->description,
            'status' => $this->status,
            'closed' => $this->closed,
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y'),
            'expenditure' => new ExpenditureResource($this->expenditure),
            'department' => $this->budgetController,
            'initiator' => new UserResource($this->initiator),
            'subBudgetHead' => $this->sub_budget_head_id > 0 ? new SubBudgetHeadResource($this->subBudgetHead) : null
        ];
    }
}
