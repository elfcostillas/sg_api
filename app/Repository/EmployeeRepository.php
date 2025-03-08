<?php

namespace App\Repository;

use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    //

    public function list()
    {
        $result = DB::connection('main')
            ->table('employees')
            ->where('emp_level','=',6)
            ->where('exit_status','=',1)
            ->select(DB::raw("id,biometric_id,lastname,firstname,concat(ifnull(lastname,''),', ',ifnull(firstname,'') )  as label"))
            ->orderBy('lastname','ASC')
            ->orderBy('firstname','ASC')
            ->get();

        return $result;
    }

    public function find($id)
    {
        
        $result = DB::connection('main')
            ->table('employees')
            ->where('id','=',$id)
            ->first();
            
        return $result;
    }
    
    public function getSGEmployee()
    {
        $result = DB::connection('main')
                ->table('employees')
                ->where('emp_level','=',6)
                ->where('exit_status','=',1)
                ->orderBy('lastname','ASC')
                ->orderBy('firstname','DESC')
                ->get();

        return $result;
    }
}
