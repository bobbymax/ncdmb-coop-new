<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    protected $guarded = [''];

    private $moduleActions = [];

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'moduleable');
    }

    public function departments()
    {
        return $this->morphedByMany(Department::class, 'moduleable');
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'moduleable');
    }

    public function grantGroupAccess(Group $group)
    {
        return $this->groups()->save($group);
    }

    public function parent()
    {
        return $this->belongsTo(Module::class, 'parentId');
    }

    public function children()
    {
        return $this->hasMany(Module::class, 'parentId');
    }

    public function permissions()
    {
        return $this->morphToMany(Permission::class, 'permissionable');
    }

    public function addPermission(Permission $permission)
    {
        return $this->permissions()->save($permission);
    }

    public function addRole(Role $role)
    {
        return $this->roles()->save($role);
    }

    public function normalizer($module)
    {
        foreach($this->actions() as $action) {
            $this->moduleActions[] = $action . " " . ucwords($module);
        }

        return $this->moduleActions;
    }

    private function actions()
    {
        return ['Browse', 'Read', 'Edit', 'Add', 'Delete'];
    }

    public function savePermission($permission, $module_name=null)
    {
        $permission = Permission::create([
            'name' => $permission,
            'key' => Str::slug($permission),
            'module' => $module_name
        ]);

        if (! $permission) {
            return null;
        }

        return $permission;
    }
}
