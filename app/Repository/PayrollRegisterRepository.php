<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class PayrollRegisterRepository
{
    //

    public function get_cols()
    {
        $result = DB::connection('main')->table('datatable_cols')
            ->select(DB::raw("module,col_value,col_name,colheader_order,alignment,width,data_type,color,style,include"))
            ->where('module','=','payreg_tbl')
            ->where('include','=','Y')
            ->get();

        return $result;
    }

    public function get_cols_other_earnings()
    {
        $result = DB::connection('main')->table('datatable_cols')
            ->select(DB::raw("module,col_value,col_name,colheader_order,alignment,width,data_type,color,style,include"))
            ->where('module','=','other_earnings')
            ->where('include','=','Y')
            ->get();

        return $result;
    }

    public function get_data($id)
    {
        $result = DB::connection('main')->table('payrollregister_unposted_weekly')
            ->join('employees','payrollregister_unposted_weekly.biometric_id','=','employees.biometric_id')
            ->join('departments','employees.dept_id','=','departments.id')
            ->join('job_titles','employees.job_title_id','=','job_titles.id')
            ->join('employee_name_vw','employee_name_vw.biometric_id','=','employees.biometric_id')
            ->leftJoin('unposted_weekly_compensation',function($join){
                $join->on('employees.id','=','unposted_weekly_compensation.emp_id');
                $join->on('payrollregister_unposted_weekly.period_id','=','unposted_weekly_compensation.period_id');
            })
            ->select(DB::raw("dept_name,employee_name_vw.employee_name,job_title_name,payrollregister_unposted_weekly.*,
                ifnull(retro_pay,0.00) retro_pay,ifnull(earnings,0.00) earnings,IFNULL(canteen_bpn,0.00) canteen_bpn,IFNULL(canteen_bps,0.00) canteen_bps,IFNULL(canteen_agg,0.00) canteen_agg,IFNULL(canteen,0.00) canteen,IFNULL(deductions,0.00) ca"))
            ->where('payrollregister_unposted_weekly.period_id','=',$id)
            ->orderBy('departments.id','ASC')
            ->orderBy('employees.lastname','ASC')
            ->orderBy('employees.firstname','ASC')
        ->get();
        
        return $result;
    }
}

//SELECT module,col_value,col_name,colheader_order,alignment,width,data_type,color,style,include 
//FROM datatable_cols WHERE module = 'payreg_tbl' AND include = 'Y'


/*

SELECT dept_name,employee_name_vw.employee_name,payrollregister_unposted_weekly.*,job_title_name
FROM employees 
LEFT JOIN departments ON employees.dept_id = departments.id 
LEFT JOIN job_titles ON employees.job_title_id = job_titles.id
INNER JOIN employee_name_vw ON employee_name_vw.biometric_id = employees.biometric_id
INNER  JOIN payrollregister_unposted_weekly ON payrollregister_unposted_weekly.biometric_id = employees.biometric_id
WHERE payrollregister_unposted_weekly.period_id = 54;

*/
