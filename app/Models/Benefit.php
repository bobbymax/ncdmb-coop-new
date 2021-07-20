<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Benefit extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function entitlements()
    {
        return $this->hasMany(Entitlement::class);
    }

    public function prices()
    {
        return $this->hasMany(PriceList::class);
    }

    public function parent()
    {
        return $this->belongsTo(Benefit::class, 'parentId');
    }

    public function children()
    {
        return $this->hasMany(Benefit::class, 'parentId');
    }
}
