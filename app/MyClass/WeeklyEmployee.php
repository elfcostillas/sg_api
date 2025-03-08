<?php

namespace App\Mappers\EmployeeFileMapper\Repository;
use Illuminate\Support\Facades\DB;

class WeeklyEmployee
{
    protected $data;
    protected $repo;
    protected $philrate;

    protected $payreg = [
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
        'actual_dblhol' => 0.0,
   
        // 'earnings'=> 0.0,
        // 'retro_pay'=> 0.0,


    ]; 

    protected $rates = [
        'monthly_credit' => null,
        'daily_rate' => null,
        'hourly_rate' => null
    ];

    public function __construct($data,$repo)
    {   
        $this->data = $data;
        $this->repo = $repo;
    }

    public function compute($period)
    {   
        
        $this->setPayRates();
        $this->payreg['daily_rate'] = $this->rates['daily_rate'];

        $this->rates['monthly_credit'] = $this->repo->getMonthlyCredit($this->data);
        
        /* Transfer employee to payeg */
        foreach($this->payreg as $key => $value){
            if(array_key_exists($key,$this->data->toArray())){
                $this->payreg[$key] = $this->data[$key];
            }
        }

        // if($this->payreg['biometric_id'] == 37 ){
        //     dd($this->payreg);
        // }

        $this->setDaysWorked();

        $this->payreg['late_eq_amount'] = round($this->rates['hourly_rate'] * $this->payreg['late_eq'],2);
        $this->payreg['under_time_amount'] = round($this->rates['hourly_rate'] * $this->payreg['under_time'],2);
        $this->payreg['absences_amount'] = round($this->rates['hourly_rate'] * $this->payreg['absences'],2);

        $this->payreg['vl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['vl_wpay'],2);
        $this->payreg['sl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['sl_wpay'],2);
        $this->payreg['bl_wpay_amount'] = round($this->rates['hourly_rate'] * $this->payreg['bl_wpay'],2);

        
        //dd($this->rates['hourly_rate']);
        /* 
            Regular Days 
            100% for non retiree
            125% for retiree

            Holiday 
            100% for non retiree
            260% for retiree
        
        */
        if($this->data['retired']=='Y'){
            $this->payreg['reg_ot_amount'] = round(($this->rates['hourly_rate'] * 1.25) * $this->payreg['reg_ot'],2);
            $this->payreg['leghol_ot_amount'] = round(($this->rates['hourly_rate'] * 2.60) * $this->payreg['leghol_ot'],2);
        }else{
            $this->payreg['reg_ot_amount'] = round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['reg_ot'],2);
            $this->payreg['leghol_ot_amount'] = round(($this->rates['hourly_rate'] * 1.0) * $this->payreg['leghol_ot'],2);
        }
        
        // $this->payreg['reg_nd_amount'] = round(($this->rates['hourly_rate'] * 0.1) * $this->payreg['reg_nd'],2);
        // $this->payreg['reg_ndot_amount'] = round(($this->rates['hourly_rate'] * 0.1 * 1.25) * $this->payreg['reg_ndot'],2);

        // /*Rest Day*/
        // $this->payreg['rd_hrs_amount'] = round(($this->rates['hourly_rate'] * 1.3) * $this->payreg['rd_hrs'],2);
        // $this->payreg['rd_ot_amount'] = round(($this->rates['hourly_rate'] * 1.3 * 1.3) * $this->payreg['rd_ot'],2);
        // $this->payreg['rd_nd_amount'] = round(($this->rates['hourly_rate'] * 1.3 * 0.1) * $this->payreg['rd_nd'],2);
        // $this->payreg['rd_ndot_amount'] = round(($this->rates['hourly_rate'] * 1.3 * 1.1 * 1.3) * $this->payreg['rd_ndot'],2);

        // /* Legal Hours */
        $this->payreg['leghol_count_amount'] =  round($this->rates['daily_rate'] * $this->payreg['leghol_count'],2);
        $this->payreg['leghol_hrs_amount'] = round($this->rates['hourly_rate'] * $this->payreg['leghol_hrs'],2);
        // $this->payreg['leghol_ot_amount'] = round($this->rates['hourly_rate'] * 2 * 1.3 * $this->payreg['leghol_ot'],2);
        // $this->payreg['leghol_nd_amount'] = round($this->rates['hourly_rate'] * 2 * 0.1 * $this->payreg['leghol_nd'],2);
        // $this->payreg['leghol_rd_amount'] = round($this->rates['hourly_rate'] * 1.6 * $this->payreg['leghol_rd'],2);
        // $this->payreg['leghol_rdnd_amount'] = round($this->rates['hourly_rate'] * 2.6 * 0.1 * $this->payreg['leghol_rdnd'],2);
        // $this->payreg['leghol_rdot_amount'] = round($this->rates['hourly_rate'] * 2.6 * 1.3 * $this->payreg['leghol_rdot'],2);
        // $this->payreg['leghol_ndot_amount'] =  round($this->rates['hourly_rate'] * 2 * 1.1 * 1.3 * $this->payreg['leghol_ndot'],2);
        // $this->payreg['leghol_rdndot_amount'] =  round($this->rates['hourly_rate'] * 2.6 * 1.1 * 1.3 * $this->payreg['leghol_rdndot'],2);

        // /* SP Holiday */
        // $this->payreg['sphol_count_amount'] = round($this->rates['daily_rate'] * $this->payreg['sphol_count'],2);
        // $this->payreg['sphol_hrs_amount'] = round($this->rates['hourly_rate'] * 0.3 * $this->payreg['sphol_hrs'],2);
        // $this->payreg['sphol_ot_amount'] = round($this->rates['hourly_rate'] * 1.3 * 1.3 * $this->payreg['sphol_ot'],2);
        // $this->payreg['sphol_nd_amount'] = round($this->rates['hourly_rate'] * 1.3 * 0.1 * $this->payreg['sphol_nd'],2);
        // $this->payreg['sphol_rd_amount'] = round($this->rates['hourly_rate'] * 0.5 * $this->payreg['sphol_rd'],2);
        // $this->payreg['sphol_rdot_amount'] = round($this->rates['hourly_rate'] * 1.5 * 1.3 * $this->payreg['sphol_rdot'],2);
        // $this->payreg['sphol_ndot_amount'] = round($this->rates['hourly_rate'] * $this->payreg['sphol_ndot'],2);
        // $this->payreg['sphol_rdndot_amount'] = round($this->rates['hourly_rate'] * 1.5 * 0.1 * 1.3 * $this->payreg['sphol_rdndot'],2);

        // $this->payreg['dblhol_count_amount'] = round($this->rates['daily_rate'] * 2 * $this->payreg['dblhol_count'],2);
        // $this->payreg['dblhol_hrs_amount'] = round($this->rates['hourly_rate'] * $this->payreg['dblhol_hrs'],2);

        // $this->payreg['dblhol_ot_amount'] = round($this->rates['hourly_rate'] * 3 * 1.3 * $this->payreg['dblhol_ot'],2);
        // $this->payreg['dblhol_nd_amount'] = round($this->rates['hourly_rate'] * 3 * 0.1 * $this->payreg['dblhol_nd'],2);
        // $this->payreg['dblhol_rd_amount'] = round($this->rates['hourly_rate'] * 3.9 * $this->payreg['dblhol_rd'],2);
        // $this->payreg['dblhol_rdot_amount'] = round($this->rates['hourly_rate'] * 3.9 * 1.3 * $this->payreg['dblhol_rdot'],2);
        // $this->payreg['dblhol_ndot_amount'] = round($this->rates['hourly_rate'] * 3 * 1.1 * 1.3 * $this->payreg['dblhol_ndot'],2);
        // $this->payreg['dblhol_rdndot_amount'] = round($this->rates['hourly_rate'] * 3.9 * 1.1 * 1.3 * $this->payreg['dblhol_rdndot'],2);

        $this->payreg['basic_pay'] = $this->repo->getBasicPay($this->payreg);

        
        if($this->data['daily_allowance']>0){
            $this->payreg['daily_allowance'] = $this->data['daily_allowance'] * $this->payreg['ndays'];
        }

        if($this->data['monthly_allowance']>0){
            $this->payreg['semi_monthly_allowance'] = round($this->data['monthly_allowance']/2,2);
        }

        // $this->payreg['gross_pay'] = $this->repo->getBasicPay($this->payreg) + $this->payreg['vl_wpay_amount'] + $this->payreg['sl_wpay_amount'] +
        //                 $this->payreg['reg_ot_amount'] +  $this->payreg['reg_nd_amount'] + $this->payreg['reg_ndot_amount'] 
        //                 + $this->payreg['rd_hrs_amount'] + $this->payreg['rd_ot_amount'] + $this->payreg['rd_nd_amount'] + $this->payreg['rd_ndot_amount'] 
        //                 + $this->payreg['leghol_count_amount'] + $this->payreg['leghol_hrs_amount'] + $this->payreg['leghol_ot_amount'] + $this->payreg['leghol_nd_amount']
        //                 + $this->payreg['leghol_rd_amount'] + $this->payreg['leghol_rdot_amount'] + $this->payreg['leghol_ndot_amount'] + $this->payreg['leghol_rdndot_amount']
        //                 + $this->payreg['sphol_count_amount'] + $this->payreg['sphol_hrs_amount'] + $this->payreg['sphol_ot_amount'] + $this->payreg['sphol_nd_amount']
        //                 + $this->payreg['sphol_rd_amount'] + $this->payreg['sphol_rdot_amount'] + $this->payreg['sphol_ndot_amount'] + $this->payreg['sphol_rdndot_amount']
        //                 + $this->payreg['dblhol_count_amount'] + $this->payreg['dblhol_hrs_amount'] + $this->payreg['dblhol_ot_amount'] + $this->payreg['dblhol_nd_amount']
        //                 + $this->payreg['dblhol_rd_amount'] + $this->payreg['dblhol_rdot_amount'] + $this->payreg['dblhol_ndot_amount'] + $this->payreg['dblhol_rdndot_amount'] + $this->payreg['semi_monthly_allowance'] + $this->payreg['daily_allowance'];

        $this->payreg['gross_pay'] = $this->repo->getGrossPay($this->payreg);

        if($this->payreg['pay_type']!='3'){
            // dd($this->payreg);
            $this->computeContribution($period);
        }
        


        /*
            Semi Monthly *
            basic_pay = (monthly_rate/2) - late - undertime - vl_wpay - sl_wpay - absent - reg_holiday_pay - sp_holiday_pay - dbl_holiday_pay
            Semi Monthly days worked
            Days Worked = 13 - late - undertime - vl - sl - absent - reg holiday - sp holiday - dbl holiday


            late_eq_amount
            under_time_amount
            vl_wpay_amount
            sl_wpay_amount
            absences_amount
            leghol_count_amount
            sphol_count_amount
        */
        
        /*
          "reg_ot" => "10.00"                          rate 1.25
            "reg_nd" => "7.00"                      rate * .10
            "reg_ndot" => "11.00"                   1.1 * 1.25
            "rd_hrs" => "12.00"                     1.3
            "rd_ot" => "13.00"                      1.3 * 1.3
            "rd_nd" => "14.00"          1.3 * 1.1
            "rd_ndot" => "15.00"        1.3 * 1.1 * 1.3
            "leghol_count" => "16.00"       
            "leghol_hrs" => "17.00"     1
            "leghol_ot" => "18.00"       2 * 1.3 
            "leghol_nd" => "21.00"             2 * 1.1
            "leghol_rd" => "19.00"          2.6
            "leghol_rdot" => "20.00"            
            "leghol_ndot" => "22.00"
            "leghol_rdndot" => "23.00"

            "sphol_count" => "24.00"
            "sphol_hrs" => "25.00"
            "sphol_ot" => "26.00"
            "sphol_nd" => "29.00"
            "sphol_rd" => "27.00"
            "sphol_rdot" => "28.00"
            "sphol_ndot" => "30.00"
            "sphol_rdndot" => "31.00"

            "dblhol_count" => "32.00"
            "dblhol_hrs" => "33.00"
            "dblhol_ot" => "34.00"
            "dblhol_nd" => "37.00"
            "dblhol_rd" => "35.00"
            "dblhol_rdot" => "36.00"
            "dblhol_ndot" => "38.00"
            "dblhol_rdndot" => "39.00"
        */


        /*** Overtime ***/
        // if($this->payreg['overtime']>0)
        // {
        //     $this->payreg['overtime_amount'] = round(($this->rates['hourly_rate'] * 1.25) * $this->payreg['overtime'],2);
        //     //dd($this->payreg['overtime_amount'],$this->payreg['overtime'],$this->rates['hourly_rate']);
        // }


        // if($this->data['sh_ot']>0){
        //     $this->payreg['sh_ot_amount'] = round(($this->rates['hourly_rate'] * 1.3) * $this->payreg['sh_ot'],2);
        // }

        // if($this->data['vl_wpay']>0){
        //    // dd($this->data['vl_wpay'] ,$this->rates['hourly_rate']);
        //    $this->payreg['vl_wpay'] = $this->data['vl_wpay'];
        //     $this->payreg['vl_wpay_amount'] = round($this->data['vl_wpay'] * $this->rates['hourly_rate'],2);
        // }

        // // if($this->data['vl_wopay']>0){

        // // }

        // if($this->data['sl_wpay']>0){
        //     $this->payreg['sl_wpay'] = $this->data['sl_wpay'];
        //     $this->payreg['sl_wpay_amount'] = round($this->data['sl_wpay'] * $this->rates['hourly_rate'],2);
            
        // }

        // $this->payreg['absences'] =  $this->data['late_eq'] + $this->data['under_time'] + $this->data['vl_wopay'] + $this->data['sl_wopay'] + $this->data['bl_wopay'];
        // $this->payreg['absences_amount'] = $this->payreg['absences'] * $this->rates['hourly_rate'];


        // if($this->data['sl_wopay']>0){

        // }
       
        /*******/

    }

    public function computeContribution($period){
       
        if($period->period_type==1){
            // $this->payreg['hdmf_contri'] = $this->data['hdmf_contri'];
            $this->payreg['sss_prem'] = 0.00;
            $this->payreg['phil_prem'] = 0.00;

        }else{
            //dd($period->period_type);
            $this->payreg['hdmf_contri'] = 0.00;
            $this->payreg['sss_prem'] = ($this->data['deduct_sss']=='Y') ?  $this->computeSSSPrem($period) : 0.00;
            $this->payreg['phil_prem'] = ($this->data['deduct_phic']=='Y') ?  round(($this->rates['monthly_credit'] * ($this->philrate/100))/2,2) : 0.00;
            $this->payreg['sss_wisp'] = ($this->data['deduct_sss']=='Y') ?  $this->computeWISP() : 0.00;
        }
    }

    public function setPayRates(){
        if($this->data['is_daily']=='Y'){
            //dd($this->data);
            $this->rates["daily_rate"] = $this->data['basic_salary'] ;//+ $this->data['daily_allowance'];
            $this->rates["hourly_rate"] = (float) round(($this->rates['daily_rate'] + $this->data['daily_allowance'])/8,4);
           
        }else{
            $this->rates["daily_rate"] = (float) round(($this->data['basic_salary']*12)/313,4);
            $this->rates["hourly_rate"] = (float) round($this->rates['daily_rate']/8,4);
        }
        
    }

    public function setPhilRate($rate){
        $this->philrate = $rate;
    }

    public function computeSSSPrem($monthly_credit)
    {
        $prem = DB::table('hris_sss_table_2025')->select('ee_share')
                //->whereRaw($this->rates['monthly_credit']." between range1 and range2")->first();
                ->whereRaw($monthly_credit." between range1 and range2")
                ->first();

     
        return (float)$prem->ee_share;
    }

    public function getMonthGross($period)
    {
        //SELECT gross_pay FROM payrollregister_posted_s WHERE period_id = '' AND biometric_id = '';
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

    public function computeWISP()
    {
        $prem = DB::table('hris_sss_table_2025')->select('mpf_ee')
                ->whereRaw($this->rates['monthly_credit']." between range1 and range2")->first();
        return (float)$prem->mpf_ee;
    }

    public function setDaysWorked()
    {
        if($this->data['is_daily']=='Y'){
            //$this->payreg['ndays'] = 0;
        }else{
            $this->payreg['ndays'] = 13 - round(($this->payreg['late_eq'] + $this->payreg['under_time'] + $this->payreg['vl_wpay'] + $this->payreg['sl_wpay'] + $this->payreg['absences'])/8,2)  - $this->payreg['leghol_count'] - $this->payreg['sphol_count'] - $this->payreg['dblhol_count'] ;
        }
    }

    public function toColumnArray()
    {
        return $this->payreg;
    }

    public function computeGrossTotal($other_earn){
        $this->payreg['gross_total'] = $this->payreg['gross_pay'];
        if($this->payreg['pay_type']!='3'){
            foreach($other_earn as $earn)
            {
                //dd($earn);
                $this->payreg['gross_total'] += $earn;
            }
        }else {

            $this->payreg['total_deduction'] = $this->payreg['hdmf_contri'] + $this->payreg['sss_prem'] + $this->payreg['phil_prem'];
           
            if($other_earn){
                // $this->payreg['retro_pay'] = $other_earn['retro_pay'];
                // $this->payreg['earnings'] = $other_earn['earnings'] ;
               
                $this->payreg['gross_total'] += $other_earn['earnings'] + $other_earn['retro_pay'];
              
                $this->payreg['total_deduction'] +=  $other_earn['deductions'] + $other_earn['canteen']   + $other_earn['cash_advance']   + $other_earn['office_account'];
            }
        }
    }

    public function computeGovContri($period)
    {
        /*
            'sss_prem' => 0.00,
            'phil_prem' => 0.00,
            'hdmf_contri' => 0.00,
        */
        $monthly_credit = 26 * $this->rates['daily_rate'];

        if($period->cut_off == 1){
            $this->payreg['hdmf_contri'] = $this->data['hdmf_contri'];
            $this->payreg['sss_prem'] = 0.00;
            $this->payreg['phil_prem'] = 0.00;

        }else{
            //dd($period->period_type);
            $this->payreg['hdmf_contri'] = 0.00;
            $this->payreg['sss_prem'] = ($this->data['deduct_sss']=='Y') ?  $this->computeSSSPrem($monthly_credit) : 0.00;
            $this->payreg['phil_prem'] = ($this->data['deduct_phic']=='Y') ?  round(($monthly_credit * ($this->philrate/100))/2,2) : 0.00;
        }

        // $period = (int) $period_id;

        /*
        if($this->data['retired'] == 'N' && $period->id > 48){
        
            $monthly_credit = 26 * $this->rates['daily_rate'];

            $this->payreg['hdmf_contri'] = round($this->data['hdmf_contri']/4,2);
            // $this->payreg['sss_prem'] = ($this->data['sss_no'] != '' && $this->data['deduct_sss'] == 'Y') ? round($this->computeSSSPrem($monthly_credit)/4,2) : 0.00;
            $this->payreg['sss_prem'] = ($this->data['deduct_sss'] == 'Y') ? round($this->computeSSSPrem($monthly_credit)/4,2) : 0.00;
            $this->payreg['phil_prem'] = ($this->data['deduct_phic']=='Y') ?  round(($monthly_credit * ($this->philrate/100))/2/4,2) : 0.00;
        }else{
            $this->payreg['hdmf_contri'] = 0;
        }
            */
        /*
        if($period->period_type==1){
            $this->payreg['hdmf_contri'] = $this->data['hdmf_contri'];
            $this->payreg['sss_prem'] = 0.00;
            $this->payreg['phil_prem'] = 0.00;

        }else{
            //dd($period->period_type);
            $this->payreg['hdmf_contri'] = 0.00;
            $this->payreg['sss_prem'] = ($this->data['deduct_sss']=='Y') ?  $this->computeSSSPrem($period) : 0.00;
            $this->payreg['phil_prem'] = ($this->data['deduct_phic']=='Y') ?  round(($this->rates['monthly_credit'] * ($this->philrate/100))/2,2) : 0.00;
            $this->payreg['sss_wisp'] = ($this->data['deduct_sss']=='Y') ?  $this->computeWISP() : 0.00;
        }
            */

        
    }

    public function computeTotalDeduction($company,$govloan)
    {
        $this->payreg['total_deduction'] = $this->payreg['hdmf_contri'] + $this->payreg['sss_prem'] + $this->payreg['phil_prem'];

        foreach($company as $loan){
            $this->payreg['total_deduction'] += $loan;
        }

        foreach($govloan as $gloan){
            $this->payreg['total_deduction'] += $gloan;
        }

    }

    public function computeNetPay()
    {
        $this->payreg['net_pay'] = $this->payreg['gross_total'] - $this->payreg['total_deduction'];
    }

    
}

/*
1-15 
    -HDMF

16-30
    -SSS Prem
    -PHIL Prem
*/

?>