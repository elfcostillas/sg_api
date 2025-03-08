<?php

namespace App\Http\Controllers\PayrollRegister;

use App\Http\Controllers\Controller;
use App\MyClass\PayrollRegister;
use App\MyClass\SG_Employee;
use App\Repository\DTRRepository;
use App\Repository\EmployeeRepository;
use App\Repository\PayrollPeriodRepository;
use App\Repository\PayrollRegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnpostedController extends Controller
{
    //

    public function __construct(public EmployeeRepository $repo,private PayrollPeriodRepository $pay_period,private DTRRepository $dtr,public PayrollRegisterRepository $payreg_repo)
    {
        
    }

    public function compute(Request $request)
    {   
        $period = $this->pay_period->getPeriod($request->id);

        $payroll = new PayrollRegister($period,$this->repo,$this->dtr);

        DB::connection('main')->table('payrollregister_unposted_weekly')->where('period_id', '=', $period->id)->delete();

        DB::connection('main')->table('payrollregister_unposted_weekly')->insert($payroll->getArray());
      
        $cols = $this->payreg_repo->get_cols();
        $earnings = $this->payreg_repo->get_cols_other_earnings();
        $data = $this->payreg_repo->get_data($period->id);
        
        $payroll->setDataAndCols($cols,$data,$earnings);

        return $this->jsonResponse($payroll->returnJson(),'OK message','success'); 

        // $sg_employees = $this->repo->getSGEmployee();

        // return json_encode($sg_employees);

        // $e = new SG_Employee();
    }
}
