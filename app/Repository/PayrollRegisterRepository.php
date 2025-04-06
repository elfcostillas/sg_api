<?php

namespace App\Repository;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayrollRegisterRepository
{
    //

    public function get_cols()
    {
        $result = DB::connection('main')->table('datatable_cols')
            ->select(DB::raw("module,col_value,col_name,colheader_order,alignment,width,data_type,color,style,include,html_width"))
            ->where('module','=','payreg_tbl')
            ->where('include','=','Y')
            ->get();

        return $result;
    }

    public function get_not_frozen()
    {
        $result = DB::connection('main')->table('datatable_cols')
            ->select(DB::raw("module,col_value,col_name,colheader_order,alignment,width,data_type,color,style,include,html_width"))
            ->where('module','=','payreg_tbl')
            ->where('include','=','Y')
            ->whereNull('freeze')
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
            ->join('employee_names_vw','employee_names_vw.biometric_id','=','employees.biometric_id')
            ->leftJoin('unposted_weekly_compensation',function($join){
                $join->on('employees.id','=','unposted_weekly_compensation.emp_id');
                $join->on('payrollregister_unposted_weekly.period_id','=','unposted_weekly_compensation.period_id');
            })
            ->select(DB::raw("dept_name,employee_names_vw.employee_name,job_title_name,payrollregister_unposted_weekly.*,
                ifnull(retro_pay,0.00) retro_pay,ifnull(earnings,0.00) earnings,IFNULL(canteen_bpn,0.00) canteen_bpn,IFNULL(canteen_bps,0.00) canteen_bps,IFNULL(canteen_agg,0.00) canteen_agg,IFNULL(canteen,0.00) canteen,IFNULL(deductions,0.00) ca"))
            ->where('payrollregister_unposted_weekly.period_id','=',$id)
            ->orderBy('departments.id','ASC')
            ->orderBy('employees.lastname','ASC')
            ->orderBy('employees.firstname','ASC')
        ->get();
        
        return $result;
    }

    public function getEmployees($period) /* Earnings and Deductions here */
    {
        $locations = DB::table('locations')->get();

        foreach($locations as $location)
        {
           
            $user = Auth::user();
            $user_id = ($user) ? $user->id : 1;
           
            $employees = DB::table('payrollregister_unposted_weekly')->select(DB::raw("employees.id,employees.biometric_id,dept_name,COALESCE(department_category.description,dept_code) dept_code,division_id,div_code,job_title_name,employee_names_vw.employee_name2 as employee_name ,payrollregister_unposted_weekly.*,employees.pay_type,employees.monthly_allowance as mallowance,
                                        employees.daily_allowance as dallowance,IF(employees.pay_type=1,employees.basic_salary/2,employees.basic_salary) AS basicpay,retired, ifnull(retro_pay,0.00) retro_pay,ifnull(earnings,0.00) earnings,IFNULL(canteen_bpn,0.00) canteen_bpn,IFNULL(canteen_bps,0.00) canteen_bps,IFNULL(canteen_agg,0.00) canteen_agg,IFNULL(canteen,0.00) canteen,IFNULL(deductions,0.00) ca"))
                                    ->join("employees",'employees.biometric_id','=','payrollregister_unposted_weekly.biometric_id')
                                    ->join("employee_names_vw",'employee_names_vw.biometric_id','=','payrollregister_unposted_weekly.biometric_id')
                                    ->leftJoin('departments','departments.id','=','employees.dept_id')
                                    ->leftJoin('divisions','divisions.id','=','employees.division_id')
                                    ->leftJoin('job_titles','employees.job_title_id','=','job_titles.id')
                                    ->leftJoin('weekly_tmp_locations',function($join) use ($period){
                                        $join->on('weekly_tmp_locations.biometric_id','=','employees.biometric_id');
                                        $join->where('weekly_tmp_locations.period_id','=',$period);
                                    })
                                    ->leftjoin('department_category','department_category.id','=','dept_category')
                                    ->where([
                                        // ['location_id','=',$location->id],
                                        ['payrollregister_unposted_weekly.period_id','=',$period],
                                        ['user_id','=',$user_id],
                                        // ['user_id','=',1],
                                    
                                    ])
                                    ->leftJoin('unposted_weekly_compensation',function($join){
                                        $join->on('employees.id','=','unposted_weekly_compensation.emp_id');
                                        $join->on('payrollregister_unposted_weekly.period_id','=','unposted_weekly_compensation.period_id');
                                    })
                                    ->whereRaw("COALESCE(weekly_tmp_locations.loc_id,employees.location_id) = $location->id")
                                    // ->orderBy('employees.pay_type','DESC')
                                    ->orderBy('departments.dept_code','DESC')
                                    ->orderBy('job_title_id','ASC')
                                    ->orderBy('employees.lastname','ASC')
                                    ->orderBy('employees.firstname','ASC')
                                    ->get();
            foreach($employees as $employee)
            {   
                // $employee->otherEarnings = $this->otherEarnings($employee->biometric_id,$period);
                // $employee->deductions = $this->deductions($employee->biometric_id,$period);
              
                $employee->deductions = $this->getDeductions($employee->period_id,$employee->id);
                $employee->gov_loans = $this->getGovLoans($employee->period_id,$employee->id);

                $employee->gov_deductions = collect(
                    [
                        'SSS Premium' => 0,
                        'SSS WISP' => 0,
                        'PhilHealt Premium' => 0,
                        'PAG IBIG Contri' => 0,
                    ]
                );

              

            }

            $location->employees = $employees;
        }
                   
        return $locations;
    }

    public function getDeductions($period_id,$emp_id)
    {   
        $ded_array = [];

        $install = DB::connection('main')->table("unposted_installments_sg")->select('deduction_type','amount')
                ->where([['emp_id','=',$emp_id],['period_id','=',$period_id]]);

        $deductions = DB::connection('main')->table('deduction_types')
                        ->select('description','deduction_type','amount')
                        ->joinSub($install,'deductions',function($join){
                            $join->on('deductions.deduction_type','=','deduction_types.id');
                        })->orderBy('deduction_type')->get();
       
        foreach($deductions as $deduction){
            if(array_key_exists($deduction->deduction_type,$ded_array)){
                $ded_array[$deduction->deduction_type] += $deduction->amount;
            }else{
                $ded_array[$deduction->deduction_type] = 0;
                $ded_array[$deduction->deduction_type] += $deduction->amount;
            }
        }

        return $ded_array;

    }

    public function getGovLoans($period_id,$emp_id)
    {   
        $ded_array = [];

        $install = DB::connection('main')->table("unposted_loans_sg")->select('deduction_type','amount')
                ->where([['emp_id','=',$emp_id],['period_id','=',$period_id]]);

        $deductions = DB::connection('main')->table('loan_types')
                        ->select('description','deduction_type','amount')
                        ->joinSub($install,'deductions',function($join){
                            $join->on('deductions.deduction_type','=','loan_types.id');
                        })->orderBy('deduction_type')->get();
       
        foreach($deductions as $deduction){
            if(array_key_exists($deduction->deduction_type,$ded_array)){
                $ded_array[$deduction->deduction_type] += $deduction->amount;
            }else{
                $ded_array[$deduction->deduction_type] = 0;
                $ded_array[$deduction->deduction_type] += $deduction->amount;
            }
        }

        return $ded_array;

    }

    public function makeRange($period)
	{
		$result = DB::table('payroll_period_weekly')
                    ->select(DB::raw("id,CONCAT(DATE_FORMAT(date_from,'%m/%d/%Y'),' - ',DATE_FORMAT(date_to,'%m/%d/%Y')) AS drange,datediff(date_to,date_from)+1 as perf"))
                    ->where('id',$period);
        return $result->first();
	}

    public function getDeductionInstallmentsCols($period){
        $result = DB::connection('main')->table('unposted_installments_sg')
        ->join('deduction_types','unposted_installments_sg.deduction_type','=','deduction_types.id')
        ->where('period_id','=',$period)
        ->select('deduction_types.id as col_value','deduction_types.description as col_name')
        ->distinct()
        ->get();

        return $result;
    }

    public function getLoanInstallmentsCols($period){
        $result = DB::connection('main')->table('unposted_loans_sg')->join('loan_types','unposted_loans_sg.deduction_type','=','loan_types.id')
        ->where('period_id','=',$period)
        ->select('loan_types.id as col_value','loan_types.description as col_name')
        ->distinct()
        ->get();

        return $result;
    }


    public function getHeaders($period)
    {
       
        if(is_object($period)){
            $period = $period->id;
        }else {
            $period = $period;
        }

        $result = DB::table('payrollregister_unposted_weekly')->select(DB::raw("SUM(reg_ot) AS reg_ot, 
        SUM(reg_ot_amount) AS reg_ot_amount,
        SUM(reg_nd) AS reg_nd,
        SUM(reg_nd_amount) AS reg_nd_amount,
        SUM(reg_ndot) AS reg_ndot,
        SUM(reg_ndot_amount) AS reg_ndot_amount,
        SUM(rd_hrs) AS rd_hrs,
        SUM(rd_hrs_amount) AS rd_hrs_amount,
        SUM(rd_ot) AS rd_ot,
        SUM(rd_ot_amount) AS rd_ot_amount,
        SUM(rd_nd) AS rd_nd,
        SUM(rd_nd_amount) AS rd_nd_amount,
        SUM(rd_ndot) AS rd_ndot,
        SUM(rd_ndot_amount) AS rd_ndot_amount,
        SUM(leghol_count) AS leghol_count,
        SUM(leghol_count_amount) AS leghol_count_amount,
        SUM(leghol_hrs) AS leghol_hrs,
        SUM(leghol_hrs_amount) AS leghol_hrs_amount,
        SUM(leghol_ot) AS leghol_ot,
        SUM(leghol_ot_amount) AS leghol_ot_amount,
        SUM(leghol_nd) AS leghol_nd,
        SUM(leghol_nd_amount) AS leghol_nd_amount,
        SUM(leghol_rd) AS leghol_rd,
        SUM(leghol_rd_amount) AS leghol_rd_amount,
        SUM(leghol_rdot) AS leghol_rdot,
        SUM(leghol_rdot_amount) AS leghol_rdot_amount,
        SUM(leghol_ndot) AS leghol_ndot,
        SUM(leghol_ndot_amount) AS leghol_ndot_amount,
        SUM(leghol_rdnd) AS leghol_rdnd,
        SUM(leghol_rdnd_amount) AS leghol_rdnd_amount,
        SUM(leghol_rdndot) AS leghol_rdndot,
        SUM(leghol_rdndot_amount) AS leghol_rdndot_amount,
        SUM(sphol_count) AS sphol_count,
        SUM(sphol_count_amount) AS sphol_count_amount,
        SUM(sphol_hrs) AS sphol_hrs,
        SUM(sphol_hrs_amount) AS sphol_hrs_amount,
        SUM(sphol_ot) AS sphol_ot,
        SUM(sphol_ot_amount) AS sphol_ot_amount,
        SUM(sphol_nd) AS sphol_nd,
        SUM(sphol_nd_amount) AS sphol_nd_amount,
        SUM(sphol_rd) AS sphol_rd,
        SUM(sphol_rd_amount) AS sphol_rd_amount,
        SUM(sphol_rdot) AS sphol_rdot,
        SUM(sphol_rdot_amount) AS sphol_rdot_amount,
        SUM(sphol_ndot) AS sphol_ndot,
        SUM(sphol_ndot_amount) AS sphol_ndot_amount,
        SUM(sphol_rdnd) AS sphol_rdnd,
        SUM(sphol_rdnd_amount) AS sphol_rdnd_amount,
        SUM(sphol_rdndot) AS sphol_rdndot,
        SUM(sphol_rdndot_amount) AS sphol_rdndot_amount,
        SUM(dblhol_count) AS dblhol_count,
        SUM(dblhol_count_amount) AS dblhol_count_amount,
        SUM(dblhol_hrs) AS dblhol_hrs,
        SUM(dblhol_hrs_amount) AS dblhol_hrs_amount,
        SUM(dblhol_ot) AS dblhol_ot,
        SUM(dblhol_ot_amount) AS dblhol_ot_amount,
        SUM(dblhol_nd) AS dblhol_nd,
        SUM(dblhol_nd_amount) AS dblhol_nd_amount,
        SUM(dblhol_rd) AS dblhol_rd,
        SUM(dblhol_rd_amount) AS dblhol_rd_amount,
        SUM(dblhol_rdot) AS dblhol_rdot,
        SUM(dblhol_rdot_amount) AS dblhol_rdot_amount,
        SUM(dblhol_ndot) AS dblhol_ndot,
        SUM(dblhol_ndot_amount) AS dblhol_ndot_amount,
        SUM(dblhol_rdnd) AS dblhol_rdnd,
        SUM(dblhol_rdnd_amount) AS dblhol_rdnd_amount,
        SUM(dblhol_rdndot) AS dblhol_rdndot,
        SUM(dblhol_rdndot_amount) AS dblhol_rdndot_amount"))
        ->where('period_id',$period);

      
        return $result->first();
    }

}

//SELECT module,col_value,col_name,colheader_order,alignment,width,data_type,color,style,include 
//FROM datatable_cols WHERE module = 'payreg_tbl' AND include = 'Y'


/*

SELECT dept_name,employee_names_vw.employee_name,payrollregister_unposted_weekly.*,job_title_name
FROM employees 
LEFT JOIN departments ON employees.dept_id = departments.id 
LEFT JOIN job_titles ON employees.job_title_id = job_titles.id
INNER JOIN employee_names_vw ON employee_names_vw.biometric_id = employees.biometric_id
INNER  JOIN payrollregister_unposted_weekly ON payrollregister_unposted_weekly.biometric_id = employees.biometric_id
WHERE payrollregister_unposted_weekly.period_id = 54;

*/
