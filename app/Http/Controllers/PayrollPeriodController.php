<?php

namespace App\Http\Controllers;

use App\Repository\PayrollPeriodRepository;
use Illuminate\Http\Request;

class PayrollPeriodController extends Controller
{
    //
    public function __construct(private PayrollPeriodRepository $repo)
    {
        
    }

    public function list(Request $request)
    {
        $filter = array();
        
        $result = $this->repo->list();

        return $this->jsonResponse($result,'OK message','success');
    }

    public function  unposted_list(Request $request)
    {
        $result = $this->repo->unposted_list();

        return $this->jsonResponse($result,'OK message','success');
    }

    public function  posted_list(Request $request)
    {
        $result = $this->repo->posted_list();

        return $this->jsonResponse($result,'OK message','success');
    }
    
}
