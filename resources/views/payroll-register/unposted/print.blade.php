<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            margin : 64px 16px 24px 16px;
        }
        * {font-size : 7pt;}
    </style>
</head>
<body>
   
    <?php

        $header_count = 3; 
        foreach($totals['overAll'] as $key => $value)
        {   
            $header_count += ($value > 0) ? 1 : 0;
            // echo $header_count. '->';
        }

        $col_count = 2;    

        $col_count += $header_count + $gov_loan->count() + $installments->count();    

    ?>
    <div style="page-break-after: always;" >
       
    <table border=0 style="width:100%;margin-bottom:2px;">
        <tr>
            <td><span style="font-size:16;" >HRD <br> Support Group Semi Monthly Payroll  </span></td>
            <td style="font-size:12pt;vertical-align:bottom" >Payroll Period :<u style="font-size:12pt;vertical-align:bottom"> {{ $period_label}} </u></td>
            <td style="width:24px" ></td>
            <td style="width:25%;font-size:12pt;padding-left:24px;vertical-align:bottom" >Date / Time  Printed: {{ date_format(now(),'m/d/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </table>

        @foreach($data as $location)
            <?php
                $ctr = 1;
                // dd($header_count ,$gov_loan->count(), $installments->count());
            ?>

            @if($location->employees->count() > 0)
                <table border=1 style="border-collapse:collapse;width:100%;margin-bottom:4px;">
                    <tr>
                        <td style="padding-left:3px;" colspan="{{ $col_count }}" > {{ $location->location_altername2 }} </td>
                    </tr>
                    <tr>
                        <td style="text-align:center;min-width:2rem;">No.</td>
                        @foreach($headers as $col)
                            <td style="text-align:center;"> {{ $col->col_name }} </td>
                        @endforeach
                        <td style="text-align:center;">Retro Pay</td>
                        <td style="text-align:center;">Earnings</td>
                        <td style="text-align:center;">Gross Pay</td>
                    
                        @if($totals['overAll']['hdmf_contri'] > 0)
                        <td style="text-align:center;">HDMF Contri</td>
                        @endif

                        @if($totals['overAll']['sss_prem'] > 0)
                        <td style="text-align:center;">SSS Prem</td>
                        @endif

                        @if($totals['overAll']['phil_prem'] > 0)
                        <td style="text-align:center;">Phil Prem</td>
                        @endif

                        @if($totals['overAll']['canteen'] > 0)
                        <td style="text-align:center;">Canteen</td>
                        @endif

                        @if($totals['overAll']['ca'] > 0)
                        <td style="text-align:center;">Cash Adv.</td>
                        @endif
                        <!-- <td style="text-align:center;">Canteen</td>
                        <td style="text-align:center;">Cash Adv.</td> -->
                        @foreach($gov_loan as $gloan)
                        <td style="text-align:center;"> {{ $gloan->col_name }} </td>
                        @endforeach

                        @foreach($installments as $installment)
                        <td style="text-align:center;min-width:5rem;max-width:5rem;"> {{ $installment->col_name }} </td>
                        @endforeach
                        <td style="text-align:center;min-width:5rem;max-width:5rem;"> Total Deduction</td>
                        <td style="text-align:center;min-width:5rem;max-width:5rem;">Net Pay</td>

                    </tr>
                    @foreach($location->employees as $employee)
                    
                        <tr>
                            <td style="padding-left:3px;"> {{ $ctr++ }} </td>
                            @foreach($headers as $col)
                                @if($col->data_type == 'number_formated')
                                    <td style="min-width:{{$col->width}};text-align:{{$col->alignment}};padding-right:3px;" > {{ ($employee->{$col->col_value} == 0) ? '' : number_format($employee->{$col->col_value},2 )  }} </td>
                                @else
                                    <td style="min-width:{{$col->width}};text-align:{{$col->alignment}};padding-left:3px;" > {{ $employee->{$col->col_value} }} </td>
                                @endif
                            @endforeach
                            <!-- <td> {{ $employee->retro_pay }} </td>
                            <td> {{ $employee->earnings }} </td>
                            <td> {{ $employee->gross_total }} </td> -->
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->retro_pay == 0) ? '' : number_format($employee->retro_pay,2) }} </td>
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->earnings == 0) ? '' : number_format($employee->earnings,2) }} </td>
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->gross_total == 0) ? '' : number_format($employee->gross_total,2) }} </td>
                            @if($totals['overAll']['hdmf_contri'] > 0)
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->hdmf_contri == 0) ? '' : number_format($employee->hdmf_contri,2) }} </td>
                            @endif

                            @if($totals['overAll']['sss_prem'] > 0)
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->sss_prem == 0) ? '' : number_format($employee->sss_prem,2) }} </td>
                            @endif

                            @if($totals['overAll']['phil_prem'] > 0)
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->phil_prem == 0) ? '' : number_format($employee->phil_prem,2) }} </td>
                            @endif

                            @if($totals['overAll']['canteen'] > 0)
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->canteen == 0) ? '' : number_format($employee->canteen,2) }} </td>
                            @endif

                            @if($totals['overAll']['ca'] > 0)
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ ($employee->ca == 0) ? '' : number_format($employee->ca,2) }} </td>
                            @endif

                            @foreach($gov_loan as $gloan)
                                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ (isset($employee->gov_loan[$gloan->col_value])) ? $employee->gov_loan[$gloan->col_value] : ''  }}</td>
                            @endforeach

                            @foreach($installments as $installment)
                                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ (isset($employee->deductions[$installment->col_value])) ? $employee->deductions[$installment->col_value] : ''  }}</td>
                            @endforeach
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;" > {{ $employee->total_deduction }} </td>
                            <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;" > {{ $employee->net_pay }} </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="6" style="text-align:right;padding-right:3px;"> <b>SUB TOTAL </b></td>
                        <!-- <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['basic_pay'],2) }}</td> -->
                        @foreach($headers as $col)
                    
                            @if($col->colheader_order >=6)
                                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;" > {{ number_format($totals['location'][$location->location_altername2][$col->col_value],2) }} </td>
                            @endif
                        @endforeach

                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;">{{ number_format($totals['location'][$location->location_altername2]['retro_pay'],2) }}</td>
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;">{{ number_format($totals['location'][$location->location_altername2]['earnings'],2) }}</td>
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;">{{ number_format($totals['location'][$location->location_altername2]['gross_total'],2) }}</td>

                        @if($totals['overAll']['hdmf_contri'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['hdmf_contri'],2) }} </td>
                        @endif

                        @if($totals['overAll']['sss_prem'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['sss_prem'],2) }} </td>
                        @endif

                        @if($totals['overAll']['phil_prem'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['phil_prem'],2) }} </td>
                        @endif

                        @if($totals['overAll']['canteen'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['canteen'],2) }} </td>
                        @endif

                        @if($totals['overAll']['ca'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['ca'],2) }} </td>
                        @endif

                        @foreach($gov_loan as $gloan)
                            @if(isset($totals['location'][$location->location_altername2]['govloans']))
                                @if(isset($totals['location'][$location->location_altername2]['govloans'][$gloan->col_value]))
                                    <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format($totals['location'][$location->location_altername2]['govloans'][$gloan->col_value],2) }} </td>
                                @else
                                    <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                                @endif
                            @else
                                <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                            @endif
                        @endforeach

                        @foreach($installments as $installment)
                            @if(isset($totals['location'][$location->location_altername2]['installment']))
                                @if(isset($totals['location'][$location->location_altername2]['installment'][$installment->col_value]))
                                    <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format($totals['location'][$location->location_altername2]['installment'][$installment->col_value],2) }} </td>
                                @else
                                    <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                                @endif
                            @else
                                <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                            @endif
                        @endforeach

                        @if($totals['overAll']['total_deduction'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['total_deduction'],2) }} </td>
                        @endif

                        @if($totals['overAll']['net_pay'] > 0)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['location'][$location->location_altername2]['net_pay'],2) }} </td>
                        @endif
                        
                    </tr>
                    
                </table>
            @endif
        @endforeach
        <table border=1 style="border-collapse:collapse;width:100%;margin-bottom:4px;">
            
            <tr>
                <td colspan=6 style="text-align:right;padding-right:3px;width:400px"> <b>GRAND TOTAL </b></td>
                @foreach($headers as $col)
            
                    @if($col->colheader_order >=6)
                        <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;" > {{ number_format($totals['overAll'][$col->col_value],2) }} </td>
                    @endif
                @endforeach

                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;">{{ number_format($totals['overAll']['retro_pay'],2) }}</td>
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;">{{ number_format($totals['overAll']['earnings'],2) }}</td>
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;">{{ number_format($totals['overAll']['gross_total'],2) }}</td>

                @if($totals['overAll']['hdmf_contri'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['hdmf_contri'],2) }} </td>
                @endif

                @if($totals['overAll']['sss_prem'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['sss_prem'],2) }} </td>
                @endif

                @if($totals['overAll']['phil_prem'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['phil_prem'],2) }} </td>
                @endif

                @if($totals['overAll']['canteen'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['canteen'],2) }} </td>
                @endif

                @if($totals['overAll']['ca'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['ca'],2) }} </td>
                @endif

                @foreach($gov_loan as $gloan)
                    @if(isset($totals['overAll']['govloans']))
                        @if(isset($totals['overAll']['govloans'][$gloan->col_value]))
                            <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format($totals['overAll']['govloans'][$gloan->col_value],2) }} </td>
                        @else
                            <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                        @endif
                    @else
                        <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                    @endif
                @endforeach

                @foreach($installments as $installment)
                    @if(isset($totals['overAll']['installment']))
                        @if(isset($totals['overAll']['installment'][$installment->col_value]))
                            <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format($totals['overAll']['installment'][$installment->col_value],2) }} </td>
                        @else
                            <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                        @endif
                    @else
                        <td style="text-align:right;padding-right:3px;min-width:5rem;max-width:5rem;"> {{ number_format(0,2) }} </td>
                    @endif
                @endforeach

                @if($totals['overAll']['total_deduction'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['total_deduction'],2) }} </td>
                @endif

                @if($totals['overAll']['net_pay'] > 0)
                <td style="min-width:5rem;max-width:5rem;text-align:right;padding-right:3px;"> {{ number_format($totals['overAll']['net_pay'],2) }} </td>
                @endif
            </tr>
        </table>
    </div>

  
    <table border=0 style="width:100%;margin-bottom:2px;">
        <tr>
            <td><span style="font-size:16;" >HRD <br>  Support Group Semi Monthly Payroll  </span></td>
            <td style="font-size:12pt;vertical-align:bottom" >Payroll Period :<u style="font-size:12pt;vertical-align:bottom"> {{ $period_label}} </u></td>
            <td style="width:24px" ></td>
            <td style="width:25%;font-size:12pt;padding-left:24px;vertical-align:bottom" >Date / Time  Printed: {{ date_format(now(),'m/d/Y H:i:s') }}</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <?php
        $empCountPerDept = empCountPerDept($data);
        $empCountPerLocationAndJobTilte = empCountPerLocationAndJobTilte($data);
        $empOTSummary = empOTSummary($data);

        $otRange = otRange($data);
        $grossPayByDept = grossPayByDept($data);
        $over9k = over9k($data);
    ?>
    <div style=""> 
        <table border=1 style="float:left;border-collapse:collapse;width:180px;">
            <tr>
                <td  style="padding:2px;text-align:center; min-width:120px;"> Employee Count Per Dept. </td>
                <td  style="padding:2px;text-align:center;min-width:32px;"> North </td>
                <td  style="padding:2px;text-align:center;min-width:32px;"> South </td>
                <td  style="padding:2px;text-align:center;min-width:32px;"> Agg </td>
                <td  style="padding:2px;text-align:center;min-width:32px;"> TOTAL </td>
            </tr>
            @foreach($empCountPerDept['depts'] as $dept)
            <tr>
                <?php
                    $mask_val2 = (in_array($dept,['Plant','Quarry'])) ? 'AGG '. $dept : $dept;
                ?>
                <td style="padding-left:2px;" > {{ $mask_val2 }}</td>    
                @foreach($empCountPerDept['result'] as $key => $value)
                <td style="padding:2px;text-align:center;min-width:32px;"> {{ isset($empCountPerDept['result'][$key][$dept]) ? $empCountPerDept['result'][$key][$dept] : '' }} </td>
                @endforeach
                <td style="padding:2px;text-align:center;min-width:32px;"> {{ isset($empCountPerDept['total_x'][$dept]) ? $empCountPerDept['total_x'][$dept] : '' }} </td>
            </tr>
            @endforeach
            <tr> @php $empTotal = 0;  @endphp
                <td style="padding-left:2px;" > TOTAL </td>
                @foreach($empCountPerDept['result'] as $key => $value)
                @php $empTotal += $empCountPerDept['total_y'][$key]; @endphp
                <td style="padding:2px;text-align:center;min-width:32px;"> {{ isset($empCountPerDept['total_y'][$key]) ? $empCountPerDept['total_y'][$key] : ''  }} </td>
                @endforeach
                <td style="padding:2px;text-align:center;min-width:32px;"> {{ $empTotal }} </td>
            </tr>
        </table>

        @foreach($empCountPerLocationAndJobTilte['data'] as $key => $value)
            @php $total = 0; @endphp
            <table border=1 style="float:left;border-collapse:collapse;margin-left : 12px;">
                <tr>
                    <td colspan="3" style="padding-left:4px;"> {{ $key }} </td>
                </tr>
                @foreach($value as $dept => $jobtitle)
                    <?php
                        $mask_dept = (in_array($dept,['Plant','Quarry'])) ? 'AGG '. $dept : $dept;
                    ?>

                    @foreach($jobtitle as $jobdesc => $count)
                    @php $total += $count; @endphp
                    <tr>
                        <td style="width: 74px;padding-left:4px;"> {{ $mask_dept }} </td>
                        <td style="width: 114px;padding-left:4px;"> {{ $jobdesc }} </td>
                        <td style="width: 34px;text-align:right;padding-right:6px;"> {{ $count }} </td>
                    </tr>
                    @endforeach
                @endforeach
                    <tr>
                        <td colspan=2 style="padding-left:4px;">TOTAL</td>
                        <td style="text-align:right;padding-right:6px;"> {{ $total }}</td>
                    </tr>
            </table>

        @endforeach

       
    </div>

    <div style="margin-top: 170px;text-align:left">
        
        <table border=1 style="border-collapse:collapse;float:left;margin-right: 6px;">
            <tr>
                <td style="width: 90px;text-align:left;padding-left: 6px;">Overtime Summary</td>
                <td></td>

                @foreach($empOTSummary as $key => $data)
                <tr>
                    <td style="width: 90px;text-align:left;padding-left: 6px;"> {{ $key }} </td>
                    <td style="width: 60px;text-align:right;padding-right: 6px;"> {{ $data }}</td>
                </tr>
                @endforeach
            </tr>
        </table>
        
        @foreach($otRange['data'] as $key => $value)
            <table border=1 style="border-collapse:collapse;float:left;margin-right: 6px;">
                <tr>
                    <td colspan=3 style="text-align: center;" > {{ $key }} </td>
                </tr>
                @foreach($value as $dept => $row)
                    <?php
                        $mask_dept = (in_array($dept,['Plant','Quarry'])) ? 'AGG '. $dept : $dept;
                    ?>
                    @foreach($row as $jobtitle => $count)
                    <tr>
                        <td style="width:80px;padding-left:4px;" > {{ $mask_dept }} </td>
                        <td style="width:100px;padding-left:4px;" > {{ $jobtitle }} </td>
                        <td style="width:40px;text-align:center;" > {{ $count }} </td>
                    </tr>
                    @endforeach
                @endforeach
            </table>
        @endforeach
    </div>
    <div style="margin-top: 90px;text-align:left">
        
        <table border=1 style="border-collapse:collapse;float:left;margin-right: 6px;">
            <tr>
                <td colspan=2 style="width: 90px;text-align:left;padding-left: 6px;">Payroll / Gross Pay</td>
            </tr>
            <?php  $perDeptTotal = 0; ?>
            @foreach($grossPayByDept as $perDeptKey => $perDeptvalue)
                <?php  $perDeptTotal +=  $perDeptvalue; 
                        $mPerDeptKey = (in_array($perDeptKey,['Plant','Quarry'])) ? 'AGG '. $perDeptKey : $perDeptKey;
                    ?>
                <tr>
                    <td style="width: 80px;text-align:left;"> {{ $mPerDeptKey }} </td>
                    <td style="width: 80px;text-align:right;"> {{ number_format($perDeptvalue,2) }} </td>
                </tr>
            @endforeach
                <tr>
                    <td style="width: 80px;text-align:left;">TOTAL</td>
                    <td style="width: 80px;text-align:right;"> {{ number_format($perDeptTotal,2) }} </td>
                </tr>
        </table>

        @if($over9k>0)
        <table border=1 style="border-collapse:collapse;float:left;margin-right: 6px;">
            <tr>
                <td style="width: 170px;text-align:center;"> Gross Pay P9,000 ++ except TM Drivers </td>
            </tr>
            <tr>
                <td style="width: 170px;text-align:center;"> {{  $over9k  }} </td>
            </tr>
        </table>
        @endif

    </div>

    <div>
        <table border=0 style="table-layout: fixed;margin-top:160px;width:60%;border-collapse:collapse;">
            <tr>
                <td></td>
                <td style="text-align:center">Prepared By : </td>
                <td></td>
                <td style="text-align:center">Checked By :</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="5" style="height:60px"></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align:center"> <u> <?php echo $prepared_by; ?> </u> </td>
                <td ></td>
                <td style="text-align:center"><u> &nbsp; Herbert B. Camasura &nbsp;</u></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="text-align:center">HR Supervisor </td>
                <td ></td>
                <td style="text-align:center">HR Manager</td>
                <td></td>
            </tr>
        </table>
    </div>
</body>
</html>

<?php

    function over9k($data)
    {
        $count = 0;

        foreach($data as $location)
        {
            foreach($location->employees as $employee)
            {
                if($employee->gross_total >= 9000)
                {
                    $count++;
                }
            }
        }

        return $count;
    }

    function grossPayByDept($data)
    {
        $depts = [];

        foreach($data as $location)
        {
            foreach($location->employees as $employee)
            {
                if(isset($depts[$employee->dept_code])){
                    $depts[$employee->dept_code] += $employee->gross_total;
                }else{
                    $depts[$employee->dept_code] = 0;
                    $depts[$employee->dept_code] += $employee->gross_total;
                }
            }
        }

        return $depts;
    }

    function otRange($data)
    {
        $table_data = [];

        $headers = [
            'Over Time >= 50++',
            'Over Time >= 60++',
            'Over Time >= 70++',
            'Over Time >= 80++',
        ];

        foreach($data as $loc)
        {  
            if($loc->employees->count()>0)
            {
                foreach($loc->employees as $employee)
                {
                    // 

                    if($employee->reg_ot >= 50 && $employee->reg_ot < 60) { 
                        if(isset($table_data['Over Time >= 50++'][$employee->dept_code][$employee->job_title_name])){
                            $table_data['Over Time >= 50++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }else{
                            $table_data['Over Time >= 50++'][$employee->dept_code][$employee->job_title_name] = 0;
                            $table_data['Over Time >= 50++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }
                    }

                    if($employee->reg_ot >= 60 && $employee->reg_ot < 70) { 
                        if(isset($table_data['Over Time >= 60++'][$employee->dept_code][$employee->job_title_name])){
                            $table_data['Over Time >= 60++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }else{
                            $table_data['Over Time >= 60++'][$employee->dept_code][$employee->job_title_name] = 0;
                            $table_data['Over Time >= 60++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }
                    }

                    if($employee->reg_ot >= 70 && $employee->reg_ot < 80) { 
                        if(isset($table_data['Over Time >= 70++'][$employee->dept_code][$employee->job_title_name])){
                            $table_data['Over Time >= 70++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }else{
                            $table_data['Over Time >= 70++'][$employee->dept_code][$employee->job_title_name] = 0;
                            $table_data['Over Time >= 70++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }
                    }

                    if($employee->reg_ot >= 80) { 
                        if(isset($table_data['Over Time >= 80++'][$employee->dept_code][$employee->job_title_name])){
                            $table_data['Over Time >= 80++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }else{
                            $table_data['Over Time >= 80++'][$employee->dept_code][$employee->job_title_name] = 0;
                            $table_data['Over Time >= 80++'][$employee->dept_code][$employee->job_title_name] += 1;
                        }
                    }
                    
                }
            }
        }

        return [
            'data' => $table_data
        ];
    }

    function empOTSummary($data)
    {
        $result = [
            '50 Hours' => 0,
            '60 Hours' => 0,
            '70 Hours' => 0,
            '80+ Hours' => 0
        ];

        foreach($data as $location)
        {
            foreach($location->employees as $employee)
            {
                
                if($employee->reg_ot >= 50 && $employee->reg_ot < 60) { $result['50 Hours'] += 1; }
                if($employee->reg_ot >= 60 && $employee->reg_ot < 70) { $result['60 Hours'] += 1; }
                if($employee->reg_ot >= 70 && $employee->reg_ot < 80) { $result['70 Hours'] += 1; }
                if($employee->reg_ot >= 80) { $result['80 Hours'] += 1; }
            }
        }

        return $result;
    }

    function empCountPerLocationAndJobTilte($data)
    {
        $location = [];
        $table_data = [];

        foreach($data as $loc)
        {   
        
            if($loc->employees->count()>0)
            {
                if(!array_key_exists($loc->location_altername2,$location)){
                    array_push($location,$loc->location_altername2); 
                }

                foreach($loc->employees as $emp)
                {
                    if(isset($table_data[$loc->location_altername2][$emp->dept_code][$emp->job_title_name])){
                        $table_data[$loc->location_altername2][$emp->dept_code][$emp->job_title_name] += 1;
                    }else{
                        $table_data[$loc->location_altername2][$emp->dept_code][$emp->job_title_name] = 0;
                        $table_data[$loc->location_altername2][$emp->dept_code][$emp->job_title_name] += 1;
                    }
                }
            }
        }

        return [
            'location' => $location,
            'data' => $table_data
        ];
    }

    function empCountPerDept($data)
    {
        $result = [];
        $depts = [];
        $total_x = [];
        $total_y = [];

        foreach($data as $location)
        {
           
            if(!isset($total_y[$location->location_altername2])){
                $total_y[$location->location_altername2] = 0;
            }

            foreach($location->employees as $employee)
            {
                if(isset($result[$location->location_altername2][$employee->dept_code])){
                    $result[$location->location_altername2][$employee->dept_code] += 1;
                }else{
                    $result[$location->location_altername2][$employee->dept_code] = 0;
                    $result[$location->location_altername2][$employee->dept_code] += 1;
                }

                if(!in_array($employee->dept_code,$depts))
                {
                    array_push($depts,$employee->dept_code);
                }

                //total by depts
                if(isset($total_x[$employee->dept_code])){
                    $total_x[$employee->dept_code] += 1;
                }else{
                    $total_x[$employee->dept_code] = 0;
                    $total_x[$employee->dept_code] += 1;
                }

                //total by locations
                if(isset($total_y[$location->location_altername2])){
                    $total_y[$location->location_altername2] += 1;
                }
                // }else{
                //     $total_y[$location->location_altername2] = 0;
                //     $total_y[$location->location_altername2] += 1;
                // }
            }
        }

        return [
            'result' => $result,        
            'depts' => $depts,     
            'total_x' => $total_x,       
            'total_y' => $total_y,      
        ];
    }
?>





