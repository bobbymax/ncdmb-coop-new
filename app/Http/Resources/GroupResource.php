<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'name' => $this->name,
            'label' => $this->label,
            'max_slots' => $this->max_slots,
            'start_date' => $this->start_date->format('Y-m-d'),
            'expiry_date' => $this->expiry_date ? $this->expiry_date->format('Y-m-d') : "",
            'cannot_expire' => $this->cannot_expire,
            'created_at' => $this->created_at->format('d F, Y')
        ];
    }
}
