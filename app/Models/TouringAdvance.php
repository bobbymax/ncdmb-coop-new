<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TouringAdvance extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $dates = ['start_date', 'end_date'];

    // public function beneficiary()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    public function controller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function claim()
    {
        return $this->belongsTo(Claim::class, 'claim_id');
    }
}
