<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class DeductionTypeRepository
{
    //

    public function deductionTypes()
    {
        $result = DB::connection('main')
        ->table('deduction_types')
        ->where('is_fixed','=','Y')
        ->select(DB::raw("id,description AS label"))
        ->get();

        return $result;
    }

    public function govtLoanTypes()
    {
        $result = DB::connection('main')
        ->table('loan_types')
        ->select(DB::raw("id,description AS label"))
        ->get();

        return $result;
    }
}


/*
select id, description AS label from deduction_types where is_fixed = 'Y';

SELECT id, description as label FROM loan_types;


*/