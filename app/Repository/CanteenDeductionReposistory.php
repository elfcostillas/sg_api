<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class CanteenDeductionReposistory
{
    //
    public function list($id)
    {
        if($id > 0){
            $query = "SELECT leftside.*,IFNULL(deductions,0.00) deductions,IFNULL(canteen_bpn,0.00) canteen_bpn,IFNULL(canteen_bps,0.00) canteen_bps,IFNULL(canteen_agg,0.00) canteen_agg,remarks2 FROM (SELECT id,biometric_id,lastname,firstname,$id AS period_id FROM employees 
            WHERE emp_level = 6 AND exit_status = 1) AS leftside LEFT JOIN (
            SELECT deductions,canteen_bpn,canteen_bps,canteen_agg,biometric_id,period_id,remarks2 FROM unposted_weekly_compensation WHERE period_id = $id
            ) AS rightside ON leftside.period_id = rightside.period_id AND leftside.biometric_id = rightside.biometric_id
            ORDER BY lastname ASC, firstname asc";

            $result = DB::connection('main')->select($query);

            return $result;
        }else{
            $query = null;
        }
       
    }

    public function insertOrUpdate($key,$data)
    {
        return DB::connection('main')->table('unposted_weekly_compensation')
        ->updateOrInsert($key,$data);
    }
}
