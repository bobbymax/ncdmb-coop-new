<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
//    protected $fillable = [
//        'name',
//        'email',
//        'password',
//    ];

    protected $guarded = [''];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function roles()
    {
        return $this->morphedByMany(Role::class, 'userable');
    }

    public function claims()
    {
        return $this->hasMany(Claim::class);
    }

    public function assignRole(Role $role)
    {
        return $this->roles()->save($role);
    }

    public function expenditures()
    {
        return $this->hasMany(Expenditure::class, 'controller_id');
    }

    public function batched()
    {
        return $this->hasMany(Batch::class, 'controller_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function departments()
    {
        return $this->morphedByMany(Department::class, 'userable');
    }

    public function currentDepartments()
    {
        return $this->departments->pluck('id')->toArray();
    }

    public function currentRoles()
    {
        return $this->roles->pluck('id')->toArray();
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function groups()
    {
        return $this->morphedByMany(Group::class, 'userable');
    }

    public function addDepartment(Department $department)
    {
        return $this->departments()->save($department);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('label', $role);
        }

        foreach ($role as $r) {
            if ($this->hasRole($r->label)) {
                return true;
            }
        }

        return false;
    }
}
