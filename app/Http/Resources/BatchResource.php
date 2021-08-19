<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'controller' => new UserResource($this->initiator),
            'batch_no' => $this->batch_no,
            'amount' => $this->amount,
            'noOfClaim' => $this->noOfClaim,
            'level' => $this->level,
            'status' => $this->status,
            'budget' => $this->budget,
            'treasury' => $this->treasury,
            'audit' => $this->audit,
            'editable' => $this->editable,
            'steps' => $this->steps,
            'expenditures' => ExpenditureResource::collection($this->expenditures)
        ];
    }
}
