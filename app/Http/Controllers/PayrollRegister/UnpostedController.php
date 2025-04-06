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
use Barryvdh\DomPDF\Facade\Pdf;

// use App\Excel\BPExport;
// use Maatwebsite\Excel\Facades\Excel;

use App\Excel\PayrollRegister as ExportPayrollRegister;
use App\MyClass\ProcessedPayrollRegister;
use App\Service\PayrollTransaction\PayrollRegisterService;
use Illuminate\Support\Facades\Auth;

class UnpostedController extends Controller
{
    //

    public function __construct(public EmployeeRepository $repo,private PayrollPeriodRepository $pay_period,private DTRRepository $dtr,public PayrollRegisterRepository $payreg_repo,public ExportPayrollRegister $excel, public PayrollRegisterService $service)
    {
        
    }

    public function compute(Request $request)
    {   
        $period = $this->pay_period->getPeriod($request->id);

        $payroll = new PayrollRegister($period,$this->repo,$this->dtr);

        DB::connection('main')->table('payrollregister_unposted_weekly')->where('period_id', '=', $period->id)->delete();

        DB::connection('main')->table('payrollregister_unposted_weekly')->insert($payroll->getArray());
      
        $cols = $this->payreg_repo->get_not_frozen();
        $earnings = $this->payreg_repo->get_cols_other_earnings();
        $data = $this->payreg_repo->get_data($period->id);
        
        $payroll->setDataAndCols($cols,$data,$earnings);

        return $this->jsonResponse($payroll->returnJson(),'OK message','success'); 

        // $sg_employees = $this->repo->getSGEmployee();

        // return json_encode($sg_employees);

        // $e = new SG_Employee();
    }

    public function excel(Request $request)
    {

        $period = $this->pay_period->getPeriod($request->id);
        $data = $this->payreg_repo->get_data($period->id);

        $cols = $this->payreg_repo->get_cols();
        $earnings = $this->payreg_repo->get_cols_other_earnings();
        $data = $this->payreg_repo->get_data($period->id);

      

        // $payroll = new ProcessedPayrollRegister($period);

        // $payroll = new PayrollRegister($period,$this->repo,$this->dtr);

        /*
         
        $result = $this->repo->getBusinessPartners($request->type);
        $this->excel->setValues($result);
        return Excel::download($this->excel,"Export.xlsx",);
        */ 

        
    }

    public function pdf(Request $request)
    {
        // dd($request->id);
        $user = Auth::user();

        $prepared_by = (is_null($user)) ? '' : $user->name;

        $period = $this->pay_period->getPeriod($request->id);
        
        $old_header = $this->payreg_repo->getHeaders($period);

        $data = $this->payreg_repo->getEmployees($period->id);
        $period_label = $this->payreg_repo->makeRange($period->id);

        $cols = $this->payreg_repo->get_cols();

        // dd($cols);
        
        $installments = $this->payreg_repo->getDeductionInstallmentsCols($period->id);
        $gov_loan = $this->payreg_repo->getLoanInstallmentsCols($period->id);

        foreach($cols as $key => $value)
        {
          
            if($value->data_type == 'number_formated')
            {
                if(property_exists($old_header,$value->col_value))
                {
                    if($old_header->{$value->col_value}<=0 ){
                        unset($cols[$key]);
                    }
                }
            }
        }

        $totals = $this->computeTotals($data,$installments,$gov_loan);

        $pdf = PDF::loadView('payroll-register.unposted.print',[
            'data' => $data,
            'headers' => $cols,
            // 'label' => $label,
            'period_label' => $period_label->drange,
            // 'perf' => $period_label->perf,
            'installments' => $installments,
            'gov_loan' => $gov_loan,
            'totals' => $totals,
            'prepared_by' => $prepared_by
        ])->setPaper('Folio','landscape');
   
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();

        // dd($cols->col_value);

        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(850, 590, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
    
        return $pdf->stream('JLR-PayrollRegister.pdf'); 
    }

    public function computeTotals($data,$installments,$gov_loan)
    {
        $location = [];
        $overAll = [];

        $keysToCompute = [
            'leghol_count',
            'leghol_count_amount',
            'leghol_hrs',
            'leghol_hrs_amount',
            'leghol_ot',
            'leghol_ot_amount',
            'sss_prem',
            'phil_prem',
            'hdmf_contri',

            // 'gross_pay',
            'gross_total',
            'net_pay',
            'total_deduction',
            'canteen',
            'ca',
            'late_eq',
            'late_eq_amount',
            'reg_ot',
            'reg_ot_amount',
            'basic_pay',

            'retro_pay',
            'earnings',
            'hdmf_contri',
            'sss_prem',
            'phil_prem',
            'canteen',
            'ca',

            'net_pay',
            'total_deduction',
            
            
        ];

        foreach($data as $loc)
        {
            foreach($loc->employees as $emp)
            {
                // $location[$loc->location_altername2]['installment'] = [];
                // $location[$loc->location_altername2]['govloans'] = [];

                if($emp->gov_loans){
                    foreach($emp->gov_loans as $dedKey => $dedValue)
                    {
                        if(isset($location[$loc->location_altername2]['govloans'][$dedKey])){
                            $location[$loc->location_altername2]['govloans'][$dedKey] += $dedValue;
                        }else{
                            $location[$loc->location_altername2]['govloans'][$dedKey] =0;
                            $location[$loc->location_altername2]['govloans'][$dedKey] += $dedValue;
                        }

                        if(isset($overAll['govloans'][$dedKey])){
                            $overAll['govloans'][$dedKey] += $dedValue;
                        }else{
                            $overAll['govloans'][$dedKey] =0;
                            $overAll['govloans'][$dedKey] += $dedValue;
                        }
                    }
                }

                if($emp->deductions){
                    foreach($emp->deductions as $dedKey => $dedValue)
                    {
                        if(isset($location[$loc->location_altername2]['installment'][$dedKey])){
                            $location[$loc->location_altername2]['installment'][$dedKey] += $dedValue;
                        }else{
                            $location[$loc->location_altername2]['installment'][$dedKey] = 0;
                            $location[$loc->location_altername2]['installment'][$dedKey] += $dedValue;
                        }
                    }

                    if(isset($overAll['installment'][$dedKey])){
                        $overAll['installment'][$dedKey] += $dedValue;
                    }else{
                        $overAll['installment'][$dedKey] =0;
                        $overAll['installment'][$dedKey] += $dedValue;
                    }
                }

                foreach($emp as $key => $value)
                {
                    if(in_array($key,$keysToCompute)){
                        if(isset($location[$loc->location_altername2][$key])){
                            $location[$loc->location_altername2][$key] += $emp->{$key};
                        }else{
                            $location[$loc->location_altername2][$key] = 0;
                            $location[$loc->location_altername2][$key] += $emp->{$key};
                        }

                        if(isset($overAll[$key])){
                            $overAll[$key] += $emp->{$key};
                        }else{
                            $overAll[$key] = 0;
                            $overAll[$key] += $emp->{$key};
                        }
                    }
                }

                
            }
        }

        return array(
            'location' => $location,
            'overAll' => $overAll
        );
    }

    public function postPayroll(Request $request)
    {
        // return response()->json($request->period_id);
        $result = $this->service->processPosting($request->period_id);

        return $this->jsonResponse($result,'OK message','success'); 

    }

    /*

      +"leghol_count": "0.00"
  +"leghol_count_amount": "0.00"
  +"leghol_hrs": "0.00"
  +"leghol_hrs_amount": "0.00"
  +"leghol_ot": "0.00"
  +"leghol_ot_amount": "0.00"

      +"module": "payreg_tbl"
  +"col_value": "dept_name"
  +"col_name": "Department"
  +"colheader_order": 1
  +"alignment": "left"
  +"width": "10rem"
  +"data_type": "string"
  +"color": "black"
  +"style": null
  +"include": "Y"
  +"html_width": "28px"
     public function downloadPdfUnposted(Request $request)
    {

        $period = $request->id;
        $headers = $this->mapper->getHeaders($period)->toArray();

        $colHeaders = $this->mapper->getColHeaders();

        foreach($headers as $key => $value){
            if($value==0){
                unset($headers[$key]);
            }
        }

        $period_label = $this->period->makeRange($period);

        foreach($colHeaders  as  $value ){
            //dd($value->var_name,$vaue->col_label);
            $label[$value->var_name] = $value->col_label;
        }

        $collections = $this->mapper->getEmployees($period);

        $pdf = PDF::loadView('app.payroll-transaction.payroll-register-weekly.print',[
                'data' => $collections,
                'headers' => $headers,
                'label' => $label,
                'period_label' => $period_label->drange,
                'perf' => $period_label->perf,
            ])->setPaper('Folio','landscape');
       
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
    
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(850, 590, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
       
        return $pdf->stream('JLR-DTR-Print.pdf'); 


    }
    */
}
