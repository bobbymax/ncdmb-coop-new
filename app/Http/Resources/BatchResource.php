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
            'queried' => $this->queried == 1 ? true : false,
            'controller' => new UserResource($this->initiator),
            'expenditures' => ExpenditureResource::collection($this->expenditures),
            'created_at' => $this->created_at->format('d F, Y'),
            'updated_at' => $this->updated_at->format('d F, Y')
        ];
    }
}
