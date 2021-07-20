<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHead extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function subBudgetHeads()
    {
        return $this->hasMany(SubBudgetHead::class);
    }

}
