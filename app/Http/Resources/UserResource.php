<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public $modules = [];
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
            'email' => $this->email,
            'staff_no' => $this->staff_no ?? "",
            'grade_level_id' => $this->grade_level_id,
            'level' => $this->gradeLevel ? $this->gradeLevel->code : "Not Set",
            'roles' => $this->roles,
            'departments' => $this->departments,
            'administrator' => $this->isAdministrator,
            'modules' => $this->getModules()
        ];
    }

    private function getModules()
    {
        if ($this->departments) {
            foreach($this->departments as $department) {
                if (is_object($department->modules) && count($department->modules) > 0) {
                    $this->modules[] = $department->modules;
                }
            }
        }

        return empty($this->modules) ? null : $this->modules;
    }
}
