<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenditureResource extends JsonResource
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
            'user_id' => $this->user_id,
            'controller' => new UserResource($this->initiator),
            'sub_budget_head_id' => $this->sub_budget_head_id,
            'subBudgetHead' => $this->subBudgetHead,
            'claim_id' => $this->claim_id,
            'claim' => $this->claim_id > 0 ? new ClaimResource($this->claim) : null,
            'beneficiary' => $this->beneficiary,
            'batch_id' => $this->batch_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'additional_info' => $this->additional_info,
            'type' => $this->type,
            'payment_type' => $this->payment_type,
            'status' => $this->status,
            'refunded' => $this->refund,
            'created_at' => $this->created_at->format('d M, Y')
            'updated_at' => $this->updated_at->format('d M, Y'),
        ];
    }
}
