<?php

namespace App\Http\Controllers\Compensation;

use App\Http\Controllers\Controller;
use App\Repository\CompensationRepository;
use Illuminate\Http\Request;

class OtherIncomeController extends Controller
{
    //
    public function __construct(public CompensationRepository $repo)
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
            'period_id' => $data['period_id'],
            'emp_id' => $data['id'],
        ];

        $data = [
            'earnings' => $data['earnings'],
            'retro_pay' => $data['retro_pay'],
            'remarks' => $data['remarks'],
            'biometric_id' => $data['biometric_id'],
        ];

        $result = $this->repo->insertOrUpdate($key,$data);

        if(is_object($result)){

        }

        return $this->json($result,'OK message','success');
        
    }

}
