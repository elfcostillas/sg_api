<?php

namespace App\Service\PayrollTransaction;

use App\Repository\PayrollRegisterRepository;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

class PayrollRegisterService
{
    //
    public function __construct(public PayrollRegisterRepository $repo)
    {
        
    }

    public function processPosting($period_id)
    {
        DB::beginTransaction();

        /* payroll register */
        $unposted = DB::table('payrollregister_unposted_weekly')
            ->where('period_id',$period_id)
            ->get();
        
        $tmp_payreg = []; 
        
        foreach($unposted as $row)
        {
            $row_arr = (array) $row;
            unset($row_arr['line_id']);
            unset($row_arr['generated_on']);

            array_push($tmp_payreg, $row_arr);
        }

        $result1 = DB::table('payrollregister_posted_weekly')->insert($tmp_payreg);

        $posted = DB::table('payrollregister_posted_weekly')
            ->where('period_id',$period_id)
            ->get();

        /****************/

        /* installments */
        $unposted_installment = DB::table('unposted_installments_sg')
            ->where('period_id',$period_id)
            ->get();

        $tmp_intsallment = [];

        foreach($unposted_installment as $row){
            $row_installment_arr = (array) $row;

            unset($row_installment_arr['line_id']);

            array_push($tmp_intsallment, $row_installment_arr);
        }

        $result2 = DB::table('posted_installments_sg')->insert($tmp_intsallment);

        $posted_installment =  DB::table('posted_installments_sg')
            ->where('period_id',$period_id)
            ->get();

        /****************/

        /* govloans */
        $unposted_govloan = DB::table('unposted_loans_sg')
            ->where('period_id',$period_id)
            ->get();

        $tmp_govloan = [];

        foreach($unposted_govloan as $row){
            $row_govloan_arr = (array) $row;
            unset($row_govloan_arr['line_id']);
            array_push($tmp_govloan, $row_govloan_arr);
        }

        $result3 = DB::table('posted_loans_sg')->insert($tmp_govloan);

        $posted_govloan =  DB::table('posted_loans_sg')
            ->where('period_id',$period_id)
            ->get();
        /****************/
        
        /* compensation */
         $unposted_compentsation = DB::table('unposted_weekly_compensation')
            ->where('period_id',$period_id)
            ->get();

        $tmp_compensation = [];

        foreach($unposted_compentsation as $row){
            $row_compensation_arr = (array) $row;
            unset($row_compensation_arr['line_id']);
            array_push($tmp_compensation, $row_compensation_arr);
        }

        $result4 = DB::table('posted_weekly_compensation')->insert($tmp_compensation);

        $posted_compensation =  DB::table('posted_weekly_compensation')
            ->where('period_id',$period_id)
            ->get();
        /****************/

        if( 
            ($unposted->count() == $posted->count()) && 
            ($unposted_installment->count() == $posted_installment->count()) &&
            ($unposted_govloan->count() == $posted_govloan->count()) &&
            ($unposted_compentsation->count() == $posted_compensation->count())
        ){
            DB::commit();

            $resp = [
                'type' => 'success',
                'msg' => 'success'
            ];
        }else{
            DB::rollBack();

            $resp = [
                'type' => 'error',
                'msg' => [],
            ];

            if($unposted->count() == $posted->count()){
                array_push( $resp['msg'],['payreg' => 'Count does not match']);
            }

            if($unposted_installment->count() == $posted_installment->count()){
                array_push( $resp['msg'],['installment' => 'Count does not match']);
            }

            if($unposted_govloan->count() == $posted_govloan->count()){
                array_push( $resp['msg'],['govloan' => 'Count does not match']);
            }

            if($unposted_compentsation->count() == $posted_compensation->count()){
                array_push( $resp['msg'],['compensation' => 'Count does not match']);
            }
        }

        return  $resp;

    }
}

