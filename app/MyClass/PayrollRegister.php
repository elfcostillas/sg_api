<?php

namespace App\MyClass;

use Illuminate\Support\Facades\DB;

class PayrollRegister
{
    //
    private $period;
    private $employee_repo;
    private $dtr_repo;

    private $employees = [];

    public $data;
    public $cols;
    public $oth_earn_cols;
    // public $earnings_col;

    public $processed_id = [];

    public function __construct($period,$employee_repo,$dtr_repo)
    {
        $this->period = $period;
        $this->employee_repo = $employee_repo;
        $this->dtr_repo = $dtr_repo;

        /* run */
        $this->getSGEmployee();

        $this->runInstallments();
        $this->runGovLoans();

        // $this->getDTR();
    }

    public function getEmpID(){

    }

    public function getSGEmployee(){
    
        $employees = $this->employee_repo->getSGEmployee();
        //select rate from philhealth
        $phic = DB::table('philhealth')->select('rate')->first();
        
        foreach($employees as $employee)
        {
            array_push($this->processed_id,$employee->id);

            $e = new SG_Employee($employee,$this->period);

            $e->setPhilRate($phic->rate);

            $dtr = $this->dtr_repo->getDTR($employee,$this->period);



            $e->setDTR($dtr);

            $e->compute();

            array_push($this->employees,$e);
        }
    }

    public function getArray()
    {
        $array = array();
        
        foreach($this->employees as $employee)
        {
            array_push($array,$employee->payreg);
        }

       return $array;
    }

    public function setDataAndCols($cols,$data,$earnings)
    {
        $this->cols = $cols;
        $this->data = $data;
        $this->oth_earn_cols = $earnings;
    }

    public function returnJson()
    {
        $assigned_deductions = $this->data;

        foreach($assigned_deductions as $emp){
            $deductions = $this->getDeductions($emp->period_id,$emp->emp_id);
            $gov_loans = $this->getGovLoans($emp->period_id,$emp->emp_id);

            $emp->deductions =  $deductions;
            $emp->gov_loans =  $gov_loans;
        }
       
        return [
            'table' => $assigned_deductions,
            'cols' => $this->cols,
            // 'oth_earn_cols' => $this->cols,
            'oth_earn_cols' => $this->oth_earn_cols,
            'installment_cols' => $this->getDeductionInstallmentsCols(),
            'loans_cols' => $this->getLoanInstallmentsCols(),

        ];
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

    public function getDeductionInstallmentsCols(){
        /*SELECT DISTINCT deduction_types.id,deduction_types.description FROM unposted_installments_sg 
        INNER JOIN deduction_types ON unposted_installments_sg.deduction_type = deduction_types.id
        WHERE period_id = 56*/

        $result = DB::connection('main')->table('unposted_installments_sg')->join('deduction_types','unposted_installments_sg.deduction_type','=','deduction_types.id')
        ->where('period_id','=',$this->period->id)
        ->select('deduction_types.id as col_value','deduction_types.description as col_name')
        ->distinct()
        ->get();

        return $result;
    }

    public function getLoanInstallmentsCols(){
        $result = DB::connection('main')->table('unposted_loans_sg')->join('loan_types','unposted_loans_sg.deduction_type','=','loan_types.id')
        ->where('period_id','=',$this->period->id)
        ->select('loan_types.id as col_value','loan_types.description as col_name')
        ->distinct()
        ->get();

        return $result;
    }

    public function runInstallments()
    {
        $tmp_loan = [];

        DB::connection('main')->table('unposted_installments_sg')->where('period_id', '=', $this->period->id)->delete();

        $loans = DB::connection('main')
        ->table('deduction_installments_sg')
        ->leftJoin('posted_installments_sg','deduction_installments_sg.id','=','posted_installments_sg.deduction_id')
        ->join('deduction_types','deduction_types.id','=','deduction_installments_sg.deduction_type')
        // ->join('employees','employees.id','=','deduction_installments_sg.emp_id')
        ->where('is_stopped','=','N')
        ->where('deduction_installments_sg.period_id','<=',$this->period->id)
        ->whereIn('deduction_types.deduction_sched',[$this->period->cut_off,3])
        ->whereIn('deduction_installments_sg.emp_id',$this->processed_id)
        ->select(DB::raw("deduction_installments_sg.id,
                                        deduction_installments_sg.biometric_id,
                                        deduction_installments_sg.emp_id,
                                        deduction_installments_sg.deduction_type,
                                        SUM(IFNULL(posted_installments_sg.amount,0)) AS paid,
                                        total_amount-SUM(IFNULL(posted_installments_sg.amount,0)) AS balance,
                                        IF(total_amount-SUM(IFNULL(posted_installments_sg.amount,0))<ammortization,total_amount-SUM(IFNULL(posted_installments_sg.amount,0)),ammortization) AS ammortization"))
        ->groupBy(DB::raw("id,deduction_installments_sg.biometric_id,deduction_installments_sg.emp_id,deduction_installments_sg.deduction_type,total_amount,ammortization")) 
        ->havingRaw('balance>0')
        ->get();

    
        if($loans)
        {
            foreach($loans as $loan)
            {
                $tmp = [
                    'period_id' => $this->period->id,
                    'biometric_id' => $loan->biometric_id,
                    'deduction_type' => $loan->deduction_type,
                    'amount' => $loan->ammortization,
                    'deduction_id' => $loan->id,
                    'emp_level' => 'sg',
                    'emp_id' => $loan->emp_id,
                    'user_id' => 1
                ];
    
                array_push($tmp_loan,$tmp);
            }
    
            DB::connection('main')->table('unposted_installments_sg')->insertOrIgnore($tmp_loan);
        }    
    }

    public function runGovLoans()
    {
        //dd($period->id);

        DB::connection('main')->table('unposted_loans_sg')
                ->where('period_id',$this->period->id)
                // ->where('user_id',$user_id)
                // ->where('emp_level',$emp_level)
                ->delete();
        
        $tmp_loan = [];

        $loans = DB::connection('main')
                            ->table('unposted_loans_sg')
                            ->select(DB::raw("deduction_gov_loans_sg.id,
                                                deduction_gov_loans_sg.emp_id,
                                                deduction_gov_loans_sg.biometric_id,
                                                deduction_gov_loans_sg.deduction_type,
                                                SUM(IFNULL(posted_loans_sg.amount,0)) AS paid,
                                                total_amount-SUM(IFNULL(posted_loans_sg.amount,0)) AS balance,
                                                IF(total_amount-SUM(IFNULL(posted_loans_sg.amount,0))<ammortization,total_amount-SUM(IFNULL(posted_loans_sg.amount,0)),ammortization) AS ammortization"))
                            ->from("deduction_gov_loans_sg")
                            ->leftJoin('posted_loans_sg','deduction_gov_loans_sg.id','=','posted_loans_sg.deduction_id')
                            ->join('loan_types','loan_types.id','=','deduction_gov_loans_sg.deduction_type')
                            ->whereRaw("is_stopped = 'N'")
                            ->whereIn('loan_types.sched',[$this->period->cut_off,3])
                            ->where('deduction_gov_loans_sg.period_id','<=',$this->period->id)
                            ->whereIn('deduction_gov_loans_sg.emp_id',$this->processed_id)
                            ->groupBy(DB::raw("id,deduction_gov_loans_sg.biometric_id,deduction_gov_loans_sg.deduction_type,deduction_gov_loans_sg.total_amount,deduction_gov_loans_sg.ammortization,emp_id"))
                            ->havingRaw('balance>0')
                            ->get();
        
        foreach($loans as $loan)
        {
            
            $tmp = [
                'period_id' => $this->period->id,
                'biometric_id' => $loan->biometric_id,
                'deduction_type' => $loan->deduction_type,
                'amount' => $loan->ammortization,
                'deduction_id' => $loan->id,
                'emp_id' => $loan->emp_id
                // 'user_id' => $user_id
            ];

            array_push($tmp_loan,$tmp);
        }

        DB::connection('main')->table('unposted_loans_sg')->insertOrIgnore($tmp_loan);

    }

    
}
