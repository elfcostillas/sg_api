<?php

namespace App\MyClass;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class SG_Employee
{
    //
    
    private $info;
    private $period;
    private $dtr;

    public $dynamic_deduction = [];
    public $dynamic_govtloan = [];

    protected $philrate;

    protected $rates = [
        'monthly_credit' => null,
        'daily_rate' => null,
        'hourly_rate' => null
    ];

    public $other_earnings = [
       'earnings' => 0.00,
       'retro_pay' => 0.00
    ];

    public $payreg = [
        'emp_id' => null,
        'emp_level' => null,
        'period_id' => null,
        'biometric_id' => null,
        'basic_pay' =>null,
        'basic_salary' => null,
        'daily_rate' => null,
        'is_daily' => null,
        'ndays' => 0.00,
        'pay_type' => null,
        'late' => 0.00,
        'late_eq' => 0.00,
        'late_eq_amount' => 0.00,
        'sss_prem' => 0.00,
        'phil_prem' => 0.00,
        'hdmf_contri' => 0.00,
        'daily_allowance' => 0.00,
        'semi_monthly_allowance' => 0.00,
        'under_time' => 0.00,
        'under_time_amount' => 0.00,
        'vl_wpay' => 0.00,
        'vl_wpay_amount' => 0.00,
        'vl_wopay' => 0.00,
        'vl_wopay_amount' => 0.00,
        'sl_wpay' => 0.00,
        'sl_wpay_amount' => 0.00,
        'sl_wopay' => 0.00, 
        'sl_wopay_amount' => 0.00,
        'bl_wpay' => 0.00,
        'bl_wpay_amount' => 0.00,
        'bl_wopay' => 0.00,
        'bl_wopay_amount' => 0.00,
        'absences' => 0.00,
        'absences_amount' => 0.00,
        'reg_ot'	=> 0.00,
        'reg_ot_amount'	=> 0.00,
        'reg_nd'	=> 0.00,
        'reg_nd_amount'	=> 0.00,
        'reg_ndot'	=> 0.00,
        'reg_ndot_amount'	=> 0.00,
        'rd_hrs'	=> 0.00,
        'rd_hrs_amount'	=> 0.00,
        'rd_ot'	=> 0.00,
        'rd_ot_amount'	=> 0.00,
        'rd_nd'	=> 0.00,
        'rd_nd_amount'	=> 0.00,
        'rd_ndot'	=> 0.00,
        'rd_ndot_amount'	=> 0.00,
        'leghol_count'	=> 0.00,
        'leghol_count_amount'	=> 0.00,
        'leghol_hrs'	=> 0.00,
        'leghol_hrs_amount'	=> 0.00,
        'leghol_ot'	=> 0.00,
        'leghol_ot_amount'	=> 0.00,
        'leghol_nd'	=> 0.00,
        'leghol_nd_amount'	=> 0.00,
        'leghol_rd'	=> 0.00,
        'leghol_rd_amount'	=> 0.00,
        'leghol_rdot'	=> 0.00,
        'leghol_rdot_amount'	=> 0.00,
        'leghol_ndot'	=> 0.00,
        'leghol_ndot_amount'	=> 0.00,
        'leghol_rdnd'	=> 0.00,
        'leghol_rdnd_amount'	=> 0.00,
        'leghol_rdndot'	=> 0.00,
        'leghol_rdndot_amount'	=> 0.00,
        'sphol_count'	=> 0.00,
        'sphol_count_amount'	=> 0.00,
        'sphol_hrs'	=> 0.00,
        'sphol_hrs_amount'	=> 0.00,
        'sphol_ot'	=> 0.00,
        'sphol_ot_amount'	=> 0.00,
        'sphol_nd'	=> 0.00,
        'sphol_nd_amount'	=> 0.00,
        'sphol_rd'	=> 0.00,
        'sphol_rd_amount'	=> 0.00,
        'sphol_rdot'	=> 0.00,
        'sphol_rdot_amount'	=> 0.00,
        'sphol_ndot'	=> 0.00,
        'sphol_ndot_amount'	=> 0.00,
        'sphol_rdnd'	=> 0.00,
        'sphol_rdnd_amount'	=> 0.00,
        'sphol_rdndot'	=> 0.00,
        'sphol_rdndot_amount'	=> 0.00,
        'dblhol_count'	=> 0.00,
        'dblhol_count_amount'	=> 0.00,
        'dblhol_hrs'	=> 0.00,
        'dblhol_hrs_amount'	=> 0.00,
        'dblhol_ot'	=> 0.00,
        'dblhol_ot_amount'	=> 0.00,
        'dblhol_nd'	=> 0.00,
        'dblhol_nd_amount'	=> 0.00,
        'dblhol_rd'	=> 0.00,
        'dblhol_rd_amount'	=> 0.00,
        'dblhol_rdot'	=> 0.00,
        'dblhol_rdot_amount'	=> 0.00,
        'dblhol_ndot'	=> 0.00,
        'dblhol_ndot_amount'	=> 0.00,
        'dblhol_rdnd'	=> 0.00,
        'dblhol_rdnd_amount'	=> 0.00,
        'dblhol_rdndot'	=> 0.00,
        'dblhol_rdndot_amount'	=> 0.00,
        'gross_pay' => 0.00,
        'gross_total' => 0.00,
        'total_deduction' => 0.00,
        'net_pay' => 0.0,
        'sss_wisp' => 0.00,
        'actual_reghol' => 0.0,
        'actual_sphol' => 0.0,

       
    ]; 

    public function __construct($info,$period)
    {
        $this->info =  (array) $info;
        $this->period = $period;

        // dd($this->info['biometric_id'],$this->period->id);

        $this->payreg['period_id'] = $this->period->id;
        $this->payreg['biometric_id'] = $this->info['biometric_id'];
        $this->payreg['user_id'] = 1;

        $this->payreg['emp_id'] = $this->info['id'];
        
        $this->payreg['generated_on'] = now();
        $this->payreg['location_id'] = $this->info['location_id'];

    }

    public function setDTR($dtr)
    {
        $this->dtr = $dtr;

        foreach($this->dtr as $day)
        {
            if(is_null($day->holiday_type)){
                $this->payreg['ndays'] += $day->ndays;
                $this->payreg['late_eq'] += $day->late_eq;

                $this->payreg['under_time'] += $day->under_time;
                $this->payreg['reg_ot'] += $day->over_time;
                $this->payreg['leghol_ot'] += $day->reghol_ot;

                // leghol_ot
                // reg_ot
                // leghol_ot
                // leghol_count
                // leghol_hrs
            }else{

                if($day->ndays > 0 || $this->checkLastWorkingDay($day->dtr_date,$this->info['location_id'],$this->info['biometric_id'])){
                    $this->payreg['leghol_count'] += 1;

                    if($day->ndays>0){
                        $this->payreg['leghol_hrs'] = round($day->ndays * 8,2);
                    }
                }
            }
        }
    }
    //$holiday,$location,$biometric_id

    public function compute()
    {
        $this->setPayRates();
        $this->payreg['daily_rate'] = $this->rates['daily_rate'];
        $this->rates['monthly_credit'] = $this->getMonthlyCredit();

        if($this->info['retired']=='Y'){
            $this->payreg['reg_ot_amount'] = round(($this->rates['hourly_rate'] * 1.25) * $this->payreg['reg_ot'],2);
            $this->payreg['leghol_ot_amount'] = round(($this->rates['hourly_rate'] * 2.60) * $this->payreg['leghol_ot'],2);
        }else{
            $this->payreg['reg_ot_amount'] = round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['reg_ot'],2);
            $this->payreg['leghol_ot_amount'] = round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['leghol_ot'],2);
        }

        $this->payreg['leghol_hrs_amount'] =  round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['leghol_hrs'],2);

        /*
        leghol_count_amount

*/
        $this->payreg['basic_salary'] = $this->info['basic_salary'];
        $this->payreg['late_eq_amount'] = round($this->rates['hourly_rate'] * $this->payreg['late_eq'],2);
        $this->payreg['under_time_amount'] = round($this->rates['hourly_rate'] * $this->payreg['under_time'],2);
       
        $this->payreg['basic_pay'] = $this->getBasicPay();

        $this->payreg['gross_pay'] = $this->getGrossPay();

        $this->getDeductions();
        $this->getGovtLoans();

        //add to gross total gross_total

        $other_earnings = $this->otherEarnings();
  
        $this->computeGrossTotal($other_earnings);

        $this->computeContribution($this->period);
        $this->computeTotalDeduction($other_earnings);

        $this->computeNetPay();

        // $this->payreg['vl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['vl_wpay'],2);
        // $this->payreg['sl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['sl_wpay'],2);
        // $this->payreg['bl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['bl_wpay'],2);

        /*
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
        */

        // $this->payreg['late_eq_amount'] = round($this->rates['hourly_rate'] * $this->payreg['late_eq'],2);
        // $this->payreg['under_time_amount'] = round($this->rates['hourly_rate'] * $this->payreg['under_time'],2);
        // $this->payreg['absences_amount'] = round($this->rates['hourly_rate'] * $this->payreg['absences'],2);

        // $this->payreg['vl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['vl_wpay'],2);
        // $this->payreg['sl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['sl_wpay'],2);
        // $this->payreg['bl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['bl_wpay'],2);

        // if($this->data['retired']=='Y'){
        //     $this->payreg['reg_ot_amount'] = round(($this->rates['hourly_rate'] * 1.25) * $this->payreg['reg_ot'],2);
        //     $this->payreg['leghol_ot_amount'] = round(($this->rates['hourly_rate'] * 2.60) * $this->payreg['leghol_ot'],2);
        // }else{
        //     $this->payreg['reg_ot_amount'] = round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['reg_ot'],2);
        //     $this->payreg['leghol_ot_amount'] = round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['leghol_ot'],2);
        // }
    
        // $this->payreg['leghol_count_amount'] =  round($this->rates['daily_rate'] * $this->payreg['leghol_count'],2);
        // $this->payreg['leghol_hrs_amount'] = round($this->rates['hourly_rate'] * $this->payreg['leghol_hrs'],2);
       




        
        // if($this->data['daily_allowance']>0){
        //     $this->payreg['daily_allowance'] = $this->data['daily_allowance'] * $this->payreg['ndays'];
        // }

        // if($this->data['monthly_allowance']>0){
        //     $this->payreg['semi_monthly_allowance'] = round($this->data['monthly_allowance']/2,2);
        // }

      
        // $this->payreg['gross_pay'] = $this->repo->getGrossPay($this->payreg);

        // if($this->payreg['pay_type']!='3'){
           
        //     $this->computeContribution($period);
        // }
        
    }

    function checkLastWorkingDay($holiday,$location,$biometric_id)
    {
        $flag = true;
        $ctr = 1;

        $isEntitled = false;

        $holiday = Carbon::createFromFormat("Y-m-d",$holiday);

        do {
            $holiday->subDay();

            if(($holiday->format('D')!='Sun' && $biometric_id != 85) || (!in_array($holiday->format('D'), ['Sun','Sat']) && $biometric_id == 85) ){
                  
                $date = DB::connection('main')->table('holidays')
                            ->join('holiday_location','holidays.id','=','holiday_location.holiday_id')
                            ->select()
                            ->where('holidays.holiday_date','=',$holiday->format('Y-m-d'))
                            ->where('holiday_location.location_id','=',$location);

                $worked = DB::connection('main')->table('edtr')->select('ndays')
                    ->where('biometric_id','=',$biometric_id)
                    ->where('dtr_date','=',$holiday->format('Y-m-d'))
                    ->first();
                
                
        
                if($date->count()<1){ // means it is not a holiday
                    $flag = false;
                    if($worked->ndays>0){
                        $isEntitled = true;
                    }
                } else {
        
                        if($worked->ndays>0){
                            $isEntitled = true;

                        }
                }

            }
            
            if($flag){
                $ctr++;
                if($ctr>=7){
                    $flag = false;
                }
            }
            
        }while($flag);

        return $isEntitled;
    }

    public function computeContribution($period){
        
        if($period->cut_off==1){
            $this->payreg['sss_prem'] = 0.00;
            $this->payreg['phil_prem'] = 0.00;

        }else{
            $monthly_credit = 26 * $this->rates['daily_rate'];

            $this->payreg['hdmf_contri'] = 0.00;
            $this->payreg['sss_prem'] = ($this->info['deduct_sss']=='Y') ?  $this->computeSSSPrem($monthly_credit) : 0.00;
            $this->payreg['phil_prem'] = ($this->info['deduct_phic']=='Y') ?  round(($this->rates['monthly_credit'] * ($this->philrate/100))/2,2) : 0.00;
            $this->payreg['sss_wisp'] = ($this->info['deduct_sss']=='Y') ?  $this->computeWISP() : 0.00;

           
        }
    }

    public function getMonthGross($period)
    {
      
        if($period['period_type']==2){
            $prev_period = $period['id'] -1;
            $prev = DB::table('payrollregister_posted_s')->select('gross_pay')
            ->where('period_id',$prev_period)
            ->where('biometric_id',$this->payreg['biometric_id'])->first();

            if($prev){
                $prev_gross = $prev->gross_pay;
            }else{
                $prev_gross = 0;
            }
            
        }

        return $this->payreg['gross_pay'] + $prev_gross;
        
    }

    public function computeSSSPrem($monthly_credit)
    {
        $prem = DB::connection('main')->table('hris_sss_table_2025')->select('ee_share')
                ->whereRaw($monthly_credit." between range1 and range2")
                ->first();

        return (float)$prem->ee_share;
    }

    public function computeWISP()
    {
        $prem = DB::connection('main')->table('hris_sss_table_2025')->select('mpf_ee')
                ->whereRaw($this->rates['monthly_credit']." between range1 and range2")->first();
        return (float)$prem->mpf_ee;
    }

    public function setPayRates(){
        if($this->info['is_daily']=='Y'){
            $this->rates["daily_rate"] = $this->info['basic_salary'] ;
            $this->rates["hourly_rate"] = (float) round(($this->rates['daily_rate'] + $this->info['daily_allowance'])/8,4);
           
        }else{
            $this->rates["daily_rate"] = (float) round(($this->info['basic_salary']*12)/313,4);
            $this->rates["hourly_rate"] = (float) round($this->rates['daily_rate']/8,4);
        }
        
    }

    public function setPhilRate($rate){
        $this->philrate = $rate;
    }

    public function computeTotalDeduction($other_earnings)
    {
        $this->payreg['total_deduction'] = $this->payreg['hdmf_contri'] + $this->payreg['sss_prem'] + $this->payreg['phil_prem'];
           
        if($other_earnings){
            
            $this->payreg['total_deduction'] +=  $other_earnings['deductions'] + $other_earnings['canteen']   + $other_earnings['cash_advance']; //  + $other_earnings['office_account'];
        }

        if($this->dynamic_deduction){
            foreach($this->dynamic_deduction as $deductions_key => $value){
                $this->payreg['total_deduction'] += $value;
            }
        }

        

    }

    public function computeGrossTotal($other_earnings){
        // $this->payreg['gross_total'] += $other_earn['earnings'] + $other_earn['retro_pay'];

        $this->payreg['gross_total'] = $this->payreg['gross_pay'] + $other_earnings['earnings'] + $other_earnings['retro_pay'];
    }

    // public function computeGovContri($period)
    // {
    //     dd();
    //     $monthly_credit = 26 * $this->rates['daily_rate'];

    //     if($period->cut_off == 1){
    //         $this->payreg['hdmf_contri'] = $this->info['hdmf_contri'];
    //         $this->payreg['sss_prem'] = 0.00;
    //         $this->payreg['phil_prem'] = 0.00;

    //     }else{
           
    //         // $this->payreg['hdmf_contri'] = 0.00;
    //         // $this->payreg['sss_prem'] = ($this->info['deduct_sss']=='Y') ?  $this->computeSSSPrem($monthly_credit) : 0.00;
    //         // $this->payreg['phil_prem'] = ($this->info['deduct_phic']=='Y') ?  round(($monthly_credit * ($this->philrate/100))/2,2) : 0.00;
    //     }
        
    // }

    public function computeNetPay()
    {
        $this->payreg['net_pay'] = $this->payreg['gross_total'] - $this->payreg['total_deduction'];
    }

    public function toColumnArray()
    {
        return $this->payreg;
    }

    public function getMonthlyCredit()
    {
        return $this->info['basic_salary'] * 26;
    }
  
    function getBasicPay()
    {
        return (float) round($this->info['basic_salary'] * $this->payreg['ndays'],2);
    }

    function getGrossPay(){
        return $this->getBasicPay() - $this->payreg['late_eq_amount'] - $this->payreg['under_time_amount'] + $this->payreg['vl_wpay_amount'] + $this->payreg['sl_wpay_amount'] + $this->payreg['bl_wpay_amount']
                        + $this->payreg['reg_ot_amount'] +  $this->payreg['reg_nd_amount'] + $this->payreg['reg_ndot_amount'] 
                        + $this->payreg['rd_hrs_amount'] + $this->payreg['rd_ot_amount'] + $this->payreg['rd_nd_amount'] + $this->payreg['rd_ndot_amount'] 
                        + $this->payreg['leghol_count_amount'] + $this->payreg['leghol_hrs_amount'] + $this->payreg['leghol_ot_amount'] + $this->payreg['leghol_nd_amount']
                        + $this->payreg['leghol_rd_amount'] + $this->payreg['leghol_rdot_amount'] + $this->payreg['leghol_ndot_amount'] + $this->payreg['leghol_rdndot_amount']
                        + $this->payreg['sphol_count_amount'] + $this->payreg['sphol_hrs_amount'] + $this->payreg['sphol_ot_amount'] + $this->payreg['sphol_nd_amount']
                        + $this->payreg['sphol_rd_amount'] + $this->payreg['sphol_rdot_amount'] + $this->payreg['sphol_ndot_amount'] + $this->payreg['sphol_rdndot_amount']
                        + $this->payreg['dblhol_count_amount'] + $this->payreg['dblhol_hrs_amount'] + $this->payreg['dblhol_ot_amount'] + $this->payreg['dblhol_nd_amount']
                        + $this->payreg['dblhol_rd_amount'] + $this->payreg['dblhol_rdot_amount'] + $this->payreg['dblhol_ndot_amount'] + $this->payreg['dblhol_rdndot_amount'] + $this->payreg['semi_monthly_allowance'] + $this->payreg['daily_allowance'];
    }

    public function otherEarnings()
    {
        $earning_array = [
            'earnings' => 0,
            'retro_pay' => 0,     

            'deductions' => 0,
            'canteen' => 0,
            'cash_advance' => 0,
            'office_account' => 0,

        ];

        $earnings = DB::connection('main')->table('unposted_weekly_compensation')->select('earnings','retro_pay','deductions','canteen','cash_advance','office_account')
        ->where('period_id','=',$this->period->id)
        ->where('emp_id','=',$this->info['id'])
        ->first();

        $this->other_earnings = [
            'earnings' => ($earnings!=null) ? $earnings->earnings : 0,
            'retro_pay' => ($earnings!=null) ? $earnings->retro_pay : 0,

            'deductions' =>  ($earnings!=null) ? $earnings->deductions : 0,
            'canteen' =>  ($earnings!=null) ? $earnings->canteen : 0,
            'cash_advance' =>  ($earnings!=null) ? $earnings->cash_advance : 0,
            'office_account' =>  ($earnings!=null) ? $earnings->office_account : 0,
        ];
        
        return $this->other_earnings;
       
    }

    public function getDeductions()
    {   
        $ded_array = [];

        $install = DB::connection('main')->table("unposted_installments_sg")
                ->select('deduction_type','amount')->where([['emp_id','=',$this->info['id']],['period_id','=',$this->period->id]]);

        $deductions = DB::connection('main')->table('deduction_types')
                        ->select('description','deduction_type','amount')
                        ->joinSub($install,'deductions',function($join){
                            $join->on('deductions.deduction_type','=','deduction_types.id');
                        })->orderBy('deduction_type')->get();
        if(count($deductions)>0){
            foreach($deductions as $deduction){
                if(array_key_exists($deduction->deduction_type,$ded_array)){
                    $ded_array[$deduction->deduction_type] += $deduction->amount;
                }else{
                    $ded_array[$deduction->deduction_type] = 0;
                    $ded_array[$deduction->deduction_type] += $deduction->amount;
                }
            }
    
            $this->dynamic_deduction = $ded_array;
        }                
       

        // return $ded_array;

    }

    public function getGovtLoans()
    {
        $ded_array = [];

        $install = DB::connection('main')->table("unposted_loans_sg")
                ->select('deduction_type','amount')->where([['emp_id','=',$this->info['id']],['period_id','=',$this->period->id]]);

        $deductions = DB::connection('main')->table('loan_types')
                        ->select('description','deduction_type','amount')
                        ->joinSub($install,'deductions',function($join){
                            $join->on('deductions.deduction_type','=','loan_types.id');
                        })->orderBy('deduction_type')->get();

        if(count($deductions)>0){
            foreach($deductions as $deduction){
                if(array_key_exists($deduction->deduction_type,$ded_array)){
                    $ded_array[$deduction->deduction_type] += $deduction->amount;
                }else{
                    $ded_array[$deduction->deduction_type] = 0;
                    $ded_array[$deduction->deduction_type] += $deduction->amount;
                }
            }

            $this->dynamic_govtloan = $ded_array;
        }
       
    }

    /*

    public function getGovLoans($biometric_id,$period_id)
    {
        $govLoan = [];
        $loan = DB::table('unposted_loans')->select('id','description','amount')
        ->join('loan_types','deduction_type','=','loan_types.id')
        ->where([['biometric_id','=',$biometric_id],['period_id','=',$period_id]])
        ->orderBy('deduction_type')->get();

        foreach($loan as $l)
        {
            if(array_key_exists($l->id,$govLoan)){
                $govLoan[$l->id] += $l->amount;
            }else{
                $govLoan[$l->id] = 0;
                $govLoan[$l->id] += $l->amount;
            }
        }
       
        return $govLoan;
    }
    */
                


}
