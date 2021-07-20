<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruction extends Model
{
    use HasFactory;

    protected $dates = ['from', 'to'];

    public function instructionable()
    {
        return $this->morphTo();
    }

    public function benefit()
    {
        return $this->belongsTo(Benefit::class, 'benefit_id');
    }

    public function category()
    {
        return $this->belongsTo(Benefit::class, 'additional_benefit_id');
    }
}
