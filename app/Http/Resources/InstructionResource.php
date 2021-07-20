<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InstructionResource extends JsonResource
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
            'benefit_id' => $this->benefit_id,
            'benefit' => $this->benefit->name,
            'additional_benefit_id' => $this->additional_benefit_id,
            'category' => $this->additional_benefit_id != 0 ? $this->category->name : null,
            'from' => $this->from->format('Y-m-d'),
            'to' => $this->to->format('Y-m-d'),
            'description' => $this->description,
            'amount' => $this->amount,
            'parent' => $this->instructionable
        ];
    }
}
