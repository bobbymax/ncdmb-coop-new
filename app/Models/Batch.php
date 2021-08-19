<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function expenditures()
    {
        return $this->hasMany(Expenditure::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'controller_id');
    }

    public function approvals()
    {
        return $this->morphMany(Approval::class, 'approveable');
    }

    private function generateBatchNo($exps)
    {
        if (! is_array($exps)) {
            return "TPF" . mt_rand(1000, 9999);
        }

        return "STP" . mt_rand(1000, 9999);
    }

    public function batchCode($exps)
    {
        $code = $this->generateBatchNo($exps);

        if (! $this->where('batch_no', $code)->first()) {
            return $code;
        }

        return $this->generateBatchNo($exps);
    }
}
