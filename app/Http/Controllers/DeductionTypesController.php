<?php

namespace App\Http\Controllers;

use App\Repository\DeductionTypeRepository;
use Illuminate\Http\Request;

class DeductionTypesController extends Controller
{
    //
    public function __construct(private DeductionTypeRepository $repo)
    {
       
    }

    public function deductionTypes()
    {
        $result = $this->repo->deductionTypes();
        return $this->jsonResponse($result,'OK message','success');
    }

    public function govtLoanTypes()
    {
        $result = $this->repo->govtLoanTypes();
        return $this->jsonResponse($result,'OK message','success');
    }


}
