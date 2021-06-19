<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entitlement extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id');
    }

    public function benefit()
    {
        return $this->belongsTo(Benefit::class, 'benefit_id');
    }
}
