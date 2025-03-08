<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class GovtLoanRepository
{
   
    public function list($filter)
    {

        $result = DB::connection('main')
            ->table('deduction_gov_loans_sg')
            ->join('loan_types','loan_types.id','=','deduction_gov_loans_sg.deduction_type')
            ->join('employees','deduction_gov_loans_sg.emp_id','=','employees.id')
            ->select(DB::raw("deduction_gov_loans_sg.*,TRIM(CONCAT(IFNULL(`employees`.`lastname`,''),', ',IFNULL(`employees`.`firstname`,''),' ',IFNULL(`employees`.`suffixname`,''))) AS `employee_name2`,loan_types.description,loan_types.description as deduction_label"));

        if(strtoupper($filter['emp_id']) != 'ALL'){
            $result->where('deduction_gov_loans_sg.emp_id','=',$filter['emp_id']);
        }

        if(strtoupper($filter['type']) != 'ALL'){
            $result->where('deduction_gov_loans_sg.deduction_type','=',$filter['type']);
        }

        $result->orderBy('deduction_gov_loans_sg.id','DESC');

        $result = $result->get();

        return $result;
    }

    public function tableHeaders()
    {
        $result = DB::connection('main')
            ->table('datatable_cols')
            ->where('module','=','loan_tbl')
            ->orderBy('colheader_order','ASC')
            ->get();

            return $result;
    }

    public function insert($array)
    {
        return DB::connection('main')
                ->table('deduction_gov_loans_sg')
                ->insertGetId($array);

    }

    public function update($array)
    {
        return DB::connection('main')
                ->table('deduction_gov_loans_sg')
                ->where('id','=',$array['id'])
                ->update($array);

    }
        
}
