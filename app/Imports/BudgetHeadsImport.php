<?php

namespace App\Imports;

use App\Models\BudgetHead;
use Maatwebsite\Excel\Concerns\ToModel;

class BudgetHeadsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new BudgetHead([
            //
        ]);
    }
}
