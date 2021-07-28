<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClaimResource extends JsonResource
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
            'reference_no' => $this->reference_no,
            'title' => $this->title,
            'status' => $this->status,
            'type' => $this->type,
            'paid' => $this->paid,
            'created_at' => $this->created_at,
            'total_amount' => $this->total_amount,
            'owner' => new UserResource($this->staff),
            'instructions' => $this->instructions
        ];
    }
}
