<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logistics extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function controller()
    {
        return $this->belongsTo(User::class, 'controller_id');
    }

    public function beneficiary()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function subBudgetHead()
    {
        return $this->belongsTo(SubBudgetHead::class, 'sub_budget_head_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
