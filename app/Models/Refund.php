<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function budgetController()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function subBudgetHead()
    {
        return $this->belongsTo(SubBudgetHead::class, 'sub_budget_head_id');
    }

    public function expenditure()
    {
        return $this->belongsTo(Expenditure::class, 'expenditure_id');
    }
}
