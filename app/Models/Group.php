<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $dates = ['start_date', 'expiry_date'];

    public function staffs()
    {
        return $this->morphToMany(User::class, 'userable');
    }

    public function modules()
    {
        return $this->morphToMany(Module::class, 'moduleable');
    }

    public function addStaff(User $user)
    {
        return $this->staffs()->save($user);
    }

    public function addModule(Module $module)
    {
        return $this->modules()->save($module);
    }
}
