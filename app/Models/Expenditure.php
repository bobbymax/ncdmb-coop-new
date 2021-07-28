<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function subBudgetHead()
    {
        return $this->belongsTo(SubBudgetHead::class, 'sub_budget_head_id');
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class, 'batch_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }
}
