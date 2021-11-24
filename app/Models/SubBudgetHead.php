<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubBudgetHead extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function budgetHead()
    {
        return $this->belongsTo(BudgetHead::class, 'budget_head_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function funds()
    {
        return $this->hasMany(CreditBudgetHead::class);
    }

    public function getCurrentFund($year)
    {
        return $this->funds->where('budget_year', $year)->first();
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }
}
