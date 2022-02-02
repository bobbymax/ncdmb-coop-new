<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
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
            'display_name' => $this->display_name,
            'key' => $this->key,
            'value' => $this->value ?? "",
            'details' => $this->details ?? "",
            'input_type' => $this->input_type,
            'group' => $this->group
        ];
    }
}
