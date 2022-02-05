<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }
}
