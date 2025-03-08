<?php

namespace App\Http\Controllers;

use App\Repository\GovtLoanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GovtLoanContoller extends Controller
{
    //

    public function __construct(private GovtLoanRepository $repo)
    {
        
    }

    public function list(Request $request)
    {
 
        $filter = array(
            'emp_id' => $request->emp_id,
            'type' => $request->dudection_type
        );
        
        $result = $this->repo->list($filter);

        return $this->jsonResponse($result,'OK message','success');
    }

    public function tableHeaders(Request $request)
    {
        $result = $this->repo->tableHeaders();

        return $this->jsonResponse($result,'OK message','success');

    }

    public function create(Request $request)
    {
        $rules = [
            'period_id' => 'required',
            'emp_id' => 'required',
            'deduction_type' => 'required',
            'remarks' => 'required',
            'total_amount' => 'required',
            'terms' => 'required',
            'ammortization' => 'required',
            'is_stopped' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->json($validator->errors(),'Error Message','error');
        }

        $validated = $validator->validated();
       
        $result = $this->repo->insert($validated);

        if(is_object($result)){

        }

        return $this->json($result,'OK message','success');
        
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required',
            'period_id' => 'required',
            'emp_id' => 'required',
            'deduction_type' => 'required',
            'remarks' => 'required',
            'total_amount' => 'required',
            'terms' => 'required',
            'ammortization' => 'required',
            'is_stopped' => 'required',
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->json($validator->errors(),'Error Message','error');
        }

        $validated = $validator->validated();

        $result = $this->repo->update($validated);

        if(is_object($result)){

        }

        return $this->json($result,'OK message','success');
    }
}

