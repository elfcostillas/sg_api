<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class CompensationRepository
{
    //

    public function list($id)
    {
        if($id > 0){
            $query = "SELECT leftside.*,ifnull(earnings,0.00) earnings,ifnull(retro_pay,0.00) retro_pay,remarks FROM (SELECT id,biometric_id,lastname,firstname,$id AS period_id FROM employees 
            WHERE emp_level = 6 AND exit_status = 1) AS leftside LEFT JOIN (
            SELECT earnings,retro_pay,biometric_id,period_id,remarks FROM unposted_weekly_compensation WHERE period_id = $id
            ) AS rightside ON leftside.period_id = rightside.period_id AND leftside.biometric_id = rightside.biometric_id
            order by lastname asc, firstname asc";

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


/*


SELECT leftside.*,earnings,retro_pay,remarks FROM (SELECT id,biometric_id,lastname,firstname,56 AS period_id FROM employees 
WHERE emp_level = 6 AND exit_status = 1) AS leftside LEFT JOIN (
SELECT earnings,retro_pay,biometric_id,period_id,remarks FROM unposted_weekly_compensation WHERE period_id = 56
) AS rightside ON leftside.period_id = rightside.period_id AND leftside.biometric_id = rightside.biometric_id
*/

