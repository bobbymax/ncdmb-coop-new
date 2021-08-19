<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFlow extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function procedures()
    {
        return $this->hasMany(Procedure::class);
    }
}
