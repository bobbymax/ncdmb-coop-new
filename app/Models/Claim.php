<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function instructions()
    {
        return $this->morphMany(Instruction::class, 'instructionable');
    }

    public function expenditure()
    {
        return $this->hasOne(Expenditure::class);
    }
}
