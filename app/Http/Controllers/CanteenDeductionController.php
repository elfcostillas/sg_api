<?php

namespace App\Http\Controllers;
use App\Repository\CanteenDeductionReposistory;
use Illuminate\Http\Request;

class CanteenDeductionController extends Controller
{
    //
    public function __construct(public CanteenDeductionReposistory $repo)
    {
        
    }

    public function list(Request $request)
    {
        $result = $this->repo->list($request->id);
        return $this->jsonResponse($result,'OK message','success');
    }

    public function save(Request $request)
    {
        $data = $request->all();

        $key = [
            'biometric_id' => $data['biometric_id'],
            'period_id' => $data['period_id'],
        ];

        $data = [
            'canteen_bpn' => $data['canteen_bpn'],
            'canteen_bps' => $data['canteen_bps'],
            'canteen_agg' => $data['canteen_agg'],
            'remarks2' => $data['remarks2'],
        ];

        $result = $this->repo->insertOrUpdate($key,$data);

        if(is_object($result)){

        }

        return $this->json($result,'OK message','success');
        
    }
}
