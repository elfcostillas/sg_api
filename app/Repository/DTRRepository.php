<?php

namespace App\Repository;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DTRRepository
{
    //

   
    public function getDTR($employee,$period){
        $dtr = DB::connection('main')
                ->table('edtr')
                ->join('employees','employees.biometric_id','=','edtr.biometric_id')
                ->join('payroll_period_weekly',function($join){
                    $join->whereRaw('dtr_date between payroll_period_weekly.date_from and payroll_period_weekly.date_to');
                })
                ->leftJoin('holidays','holidays.holiday_date','=','edtr.dtr_date')
                ->leftJoin('holiday_location',function($join){
                    $join->on('holiday_location.holiday_id','=','holidays.id');
                    $join->on('holiday_location.location_id','=','employees.location_id');
                })
                ->select(DB::raw("edtr.*,DATE_FORMAT(dtr_date,'%a') as day_name,holiday_type"))
                ->where('payroll_period_weekly.id', '=', $period->id)
                ->where('employees.biometric_id','=',$employee->biometric_id)
                ->get();
        return $dtr;
    }

    public function getEmployeeWithDTRW($period_id,$emp_level)
    {
        $result = DB::connection('main')
                        ->table('edtr')->select(DB::raw("
                        'weekly' AS emp_level,
                        payroll_period_weekly.id AS period_id,
                        employees.biometric_id,
                        lastname,
                        firstname,
                        middlename,
                        suffixname,
                        basic_salary,
                        date_hired,
                        is_daily,
                        deduct_phic,
                        deduct_sss,
                        pay_type,
                        SUM(late) AS late,
                        SUM(late_eq) AS late_eq,
                        SUM(under_time) AS under_time,
                        SUM(over_time) AS reg_ot,
                        SUM(night_diff) AS reg_nd,
                        SUM(night_diff_ot) AS reg_ndot,
                        SUM(ndays) AS ndays,
                        hdmf_contri,
                        monthly_allowance,
                        daily_allowance,
                        sum(restday_hrs) as rd_hrs,
                        sum(restday_ot) as rd_ot,
                        sum(restday_nd) as rd_nd,
                        sum(restday_ndot) as rd_ndot,

                        sum(reghol_pay) as leghol_count,
                        sum(reghol_hrs) as leghol_hrs,
                        sum(reghol_ot) as leghol_ot,
                        sum(reghol_rd) as leghol_rd,
                        sum(reghol_rdot) as leghol_rdot,
                        sum(reghol_nd) as leghol_nd,
                        sum(reghol_rdnd) as leghol_rdnd,
                        sum(reghol_ndot) as leghol_ndot,
                        sum(reghol_rdndot) as leghol_rdndot,

                        sum(sphol_pay) as sphol_count,
                        sum(sphol_hrs) as sphol_hrs,
                        sum(sphol_ot) as sphol_ot,
                        sum(sphol_rd) as sphol_rd,
                        sum(sphol_rdot) as sphol_rdot,
                        sum(sphol_nd) as sphol_nd,
                        sum(sphol_rdnd) as sphol_rdnd,
                        sum(sphol_ndot) as sphol_ndot,
                        sum(sphol_rdndot) as sphol_rdndot,

                        sum(dblhol_pay) as dblhol_count,
                        sum(dblhol_hrs) as dblhol_hrs,
                        sum(dblhol_ot) as dblhol_ot,
                        sum(dblhol_rd) as dblhol_rd,
                        sum(dblhol_rdot) as dblhol_rdot,
                        sum(dblhol_rdnd) as dblhol_rdnd,
                        sum(dblhol_nd) as dblhol_nd,
                        sum(dblhol_ndot) as dblhol_ndot,
                        sum(dblhol_rdndot) as dblhol_rdndot,
                        retired
                        "))
                    ->from('edtr')
                    ->join('payroll_period_weekly',function($join){
                        $join->whereRaw('edtr.dtr_date between payroll_period_weekly.date_from and payroll_period_weekly.date_to');
                    })
                    ->join('employees','edtr.biometric_id','=','employees.biometric_id')
                   
                    ->where('payroll_period_weekly.id','=',$period_id)
                    ->where('exit_status',1)
                 
                    ->where(function($query){
                        $query->where('ndays','>',0)
                        ->orWhere('reghol_pay','>',0)
                        ->orWhere('over_time','>',0);
                    })
                    ->groupBy(DB::raw('
                                payroll_period_weekly.id,
                                employees.biometric_id,
                                lastname,
                                firstname,
                                middlename,
                                suffixname,
                                basic_salary,
                                is_daily,
                                deduct_phic,
                                deduct_sss,
                                pay_type, 
                                hdmf_contri,
                                monthly_allowance,
                                daily_allowance'));
                    // ->havingRaw('SUM(ndays) > ?', [0]);
                                
                $result = $result->where('emp_level','=',6);
           

        return $result->get();
    }

    
   
}



/*
select * from edtr
inner join employees on  edtr.biometric_id = employees.biometric_id
inner join payroll_period_weekly on edtr.dtr_date between payroll_period_weekly.date_from and payroll_period_weekly.date_to
left join holidays on holidays.holiday_date between payroll_period_weekly.date_from and payroll_period_weekly.date_to
left join holiday_location on holidays.id = holiday_location.holiday_id and holiday_location.location_id = employees.location_id
where payroll_period_weekly.id = 56
AND employees.biometric_id = 847
*/
