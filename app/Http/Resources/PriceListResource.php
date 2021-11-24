<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PriceListResource extends JsonResource
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
            'benefit_name' => $this->benefit->name,
            'amount' => $this->amount,
            'benefit' => new BenefitResource($this->benefit),
            'created_at' => $this->created_at->format('d F, Y')
        ];
    }
}
