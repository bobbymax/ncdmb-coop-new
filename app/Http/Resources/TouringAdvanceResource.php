<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TouringAdvanceResource extends JsonResource
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
            'user_id' => $this->user_id,
            'controller' => new UserResource($this->controller),
            'reference_no' => $this->reference_no,
            'start_date' => $this->start_date->format("Y-m-d"),
            'end_date' => $this->end_date->format("Y-m-d"),
            'claim' => new ClaimResource($this->claim),
            'status' => $this->status,
            'claim_status' => $this->claim_status,
            'closed' => $this->closed == 1 ? true : false
        ];
    }
}
