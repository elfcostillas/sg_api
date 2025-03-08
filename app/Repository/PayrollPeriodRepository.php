<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class PayrollPeriodRepository
{
    //

    public function list()
    {
        $result = DB::connection('main')
            ->table('payroll_period_weekly')
            ->select(DB::raw("id,concat(date_format(date_from,'%m/%d/%Y') ,' - ',DATE_FORMAT(date_to,'%m/%d/%Y')) as label"))
            ->orderBy('id','desc')
            ->get();

        return $result;
    }

    public function getPeriod($id)
    {
        return DB::connection('main')
                ->table('payroll_period_weekly')
                ->where('id','=',$id)
                ->select('id','date_from','date_to','date_release','man_hours','pyear','cut_off')
                ->first();
    }
    
    public function unposted_list()
    {
        $result = DB::connection('main')
            ->table('payroll_period_weekly')
            ->leftJoin('payrollregister_posted_weekly','payroll_period_weekly.id','=','payrollregister_posted_weekly.period_id')
            ->select(DB::raw("payroll_period_weekly.id,payroll_period_weekly.date_from,payroll_period_weekly.date_to,CONCAT(DATE_FORMAT(payroll_period_weekly.date_from,'%m/%d/%Y'),' - ',DATE_FORMAT(payroll_period_weekly.date_to,'%m/%d/%Y')) AS label"))
            ->distinct()
            ->whereNull('payrollregister_posted_weekly.period_id')
            ->orderBy('payroll_period_weekly.id','DESC')
            ->get();
        
        return $result;
    }

    public function posted_list()
    {
        $result = DB::connection('main')
        ->table('payroll_period_weekly')
        ->leftJoin('payrollregister_posted_weekly','payroll_period_weekly.id','=','payrollregister_posted_weekly.period_id')
        ->select(DB::raw("payroll_period_weekly.id,payroll_period_weekly.date_from,payroll_period_weekly.date_to,CONCAT(DATE_FORMAT(payroll_period_weekly.date_from,'%m/%d/%Y'),' - ',DATE_FORMAT(payroll_period_weekly.date_to,'%m/%d/%Y')) AS label"))
        ->distinct()
        ->whereNotNull('payrollregister_posted_weekly.period_id')
        ->orderBy('payroll_period_weekly.id','DESC')
        ->get();
    
    return $result;
    }

    public function getCols()
    {
        $result = DB::connection('main')
        ->table('payroll_period_weekly')
       
        ->get();
    }

    public function getProcessed()
    {

    }
}

/*select id,concat(date_format(date_from,'%m/%d/%Y') ,' - ',DATE_FORMAT(date_to,'%m/%d/%Y')) as label 
from payroll_period_weekly order by id desc;


SELECT DISTINCT payroll_period_weekly.id,payroll_period_weekly.date_from,payroll_period_weekly.date_to,CONCAT(DATE_FORMAT(payroll_period_weekly.date_from,'%m/%d/%Y'),' - ',DATE_FORMAT(payroll_period_weekly.date_to,'%m/%d/%Y')) AS label
FROM payroll_period_weekly 
LEFT JOIN payrollregister_posted_weekly ON payroll_period_weekly.id = payrollregister_posted_weekly.period_id
WHERE payrollregister_posted_weekly.period_id IS NULL
ORDER BY id DESC

*/