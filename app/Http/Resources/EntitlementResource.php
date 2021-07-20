<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EntitlementResource extends JsonResource
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
            'grade_level_id' => $this->grade_level_id,
            'grade' => $this->gradeLevel->code,
            'benefit' => $this->benefit->name,
            'amount' => $this->price ? $this->price->amount : 0
        ];
    }
}
