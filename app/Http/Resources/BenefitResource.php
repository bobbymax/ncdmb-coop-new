<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\GradeLevel;

class BenefitResource extends JsonResource
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
            'parentId' => $this->parentId,
            'description' => $this->description,
            'parent' => $this->parentId > 0 ? $this->parent->name : 'No Parent',
            'entitlements' => $this->entitlements ? EntitlementResource::collection($this->entitlements) : null,
            'wages' => $this->prices,
            'canAddEntitlement' => $this->canAddEntitlementCheck(),
            'children' => BenefitResource::collection($this->children),
            'hasChildren' => count($this->children) > 0 ? true : false,
            'numOfDays' => $this->numOfDays == 1 ? true : false
        ];
    }

    protected function canAddEntitlementCheck()
    {
        $grades = GradeLevel::all();

        if($this->entitlements->count() < $grades->count()) {
            return true;
        }

        return false;
    }
}
