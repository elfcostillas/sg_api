<?php

namespace App\Http\Controllers;

use App\Repository\EmployeeRepository;
use App\Service\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    public function __construct(private EmployeeService $service, private EmployeeRepository $repo)
    {
        
    }

    public function list()
    {
        $result = $this->repo->list();
        return $this->jsonResponse($result,'OK message','success');
    }

    public function find(Request $request)
    {
        $result = $this->repo->find($request->id);
        return $this->json($result,'OK message','success');
    }
}
