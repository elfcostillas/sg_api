<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class DeductionRepository
{
    //

    public function list($filter)
    {

//   "emp_id" => "574"
//   "type" => "11"

        $result = DB::connection('main')
            ->table('deduction_installments_sg')
            ->join('deduction_types','deduction_types.id','=','deduction_installments_sg.deduction_type')
            ->join('employees','deduction_installments_sg.emp_id','=','employees.id')
            ->select(DB::raw("deduction_installments_sg.*,TRIM(CONCAT(IFNULL(`employees`.`lastname`,''),', ',IFNULL(`employees`.`firstname`,''),' ',IFNULL(`employees`.`suffixname`,''))) AS `employee_name2`,deduction_types.description,deduction_types.description as deduction_label"));

        if(strtoupper($filter['emp_id']) != 'ALL'){
            $result->where('deduction_installments_sg.emp_id','=',$filter['emp_id']);
        }

        if(strtoupper($filter['type']) != 'ALL'){
            $result->where('deduction_installments_sg.deduction_type','=',$filter['type']);
        }

        $result->orderBy('deduction_installments_sg.id','DESC');

        $result = $result->get();

        return $result;
    }

    public function tableHeaders()
    {
        $result = DB::connection('main')
            ->table('datatable_cols')
            ->where('module','=','deduction_tbl')
            ->orderBy('colheader_order','ASC')
            ->get();

            return $result;
    }

    public function insert($array)
    {
        return DB::connection('main')
                ->table('deduction_installments_sg')
                ->insertGetId($array);

    }

    public function update($array)
    {
        return DB::connection('main')
                ->table('deduction_installments_sg')
                ->where('id','=',$array['id'])
                ->update($array);

    }




    
}


/*

SELECT col_name,col_value FROM datatable_cols WHERE module = 'deduction_tbl';

select deduction_installments_sg.*,TRIM(CONCAT(IFNULL(`employees`.`lastname`,''),', ',IFNULL(`employees`.`firstname`,''),' ',IFNULL(`employees`.`suffixname`,''))) AS `employee_name2`,deduction_types.description
from deduction_installments_sg 
inner join deduction_types on deduction_types.id = deduction_installments_sg.deduction_type
inner join employees on deduction_installments_sg.biometric_id = employees.biometric_id
order by deduction_installments_sg.id desc;

*/