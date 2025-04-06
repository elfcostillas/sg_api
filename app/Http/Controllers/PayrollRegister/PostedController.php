<?php

namespace App\Http\Controllers\PayrollRegister;

use App\Http\Controllers\Controller;
use App\Repository\EmployeeRepository;
use App\Repository\PayrollPeriodRepository;
use App\Repository\PayrollRegisterRepository;
use Illuminate\Http\Request;

class PostedController extends Controller
{
    //
    public function __construct(
        public EmployeeRepository $emp_repo,
        private PayrollPeriodRepository $pay_period,
        public PayrollPeriodRepository $period_repo,
        public PayrollRegisterRepository $payreg_repo
        ){
         
    }

    public function computed(Request $request)
    {
        $period = $this->pay_period->getPeriod($request->id);

        dd($period);
    }
}
