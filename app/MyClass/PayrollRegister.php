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

        // $this->getDTR();
    }

    public function getEmpID(){

    }

    public function getSGEmployee(){
    
        $employees = $this->employee_repo->getSGEmployee();

        foreach($employees as $employee)
        {
            array_push($this->processed_id,$employee->id);

            $e = new SG_Employee($employee,$this->period);

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
       
        return [
            'table' => $this->data,
            'cols' => $this->cols,
            // 'oth_earn_cols' => $this->cols,
            'oth_earn_cols' => $this->oth_earn_cols,
        ];
    }

    public function runInstallments()
    {
        $tmp_loan = [];

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

    
}
