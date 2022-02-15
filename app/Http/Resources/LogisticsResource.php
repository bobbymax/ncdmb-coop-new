<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LogisticsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'controller_id' => $this->controller_id,
            'user_id' => $this->user_id,
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'department_id' => $this->department_id,
            'beneficiary' => new UserResource($this->beneficiary),
            'subBudgetHead' => new SubBudgetHeadResource($this->subBudgetHead),
            'description' => $this->description,
            'amount' => $this->amount,
            'status' => $this->status,
            'closed' => $this->closed
        ];
    }
}
