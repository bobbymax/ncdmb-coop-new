<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            'icon' => $this->icon ?? "",
            'path' => $this->path ?? "",
            'component' => $this->component ?? "",
            'parentId' => $this->parentId,
            'parent' => $this->parentId != 0 ? $this->parent->name : 'None',
            'isAuthRequired' => $this->isAuthRequired,
            'isAdministration' => $this->isAdministration,
            'isMenu' => $this->isMenu,
            'roles' => $this->roles,
            'departments' => $this->departments,
            'permissions' => $this->generatePermissions ? $this->permissions : [],
            'children' => ModuleResource::collection($this->children)
        ];
    }
}
