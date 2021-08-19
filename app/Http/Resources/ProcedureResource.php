<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProcedureResource extends JsonResource
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
            'work_flow_id' => $this->work_flow_id,
            'role_id' => $this->role_id,
            'order' => $this->order,
            'workflow' => $this->workflow,
            'role' => $this->role
        ];
    }
}
