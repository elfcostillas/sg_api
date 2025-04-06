<?php

use App\Http\Controllers\Compensation\OtherIncomeController;
use App\Http\Controllers\DeductionsController;
use App\Http\Controllers\DeductionTypesController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GovtLoanContoller;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayrollRegister\UnpostedController;
use App\Http\Controllers\CanteenDeductionController;
use App\Http\Controllers\PayrollRegister\PostedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Route::prefix('upload-log')->group(function(){
//     Route::get('/',[UploadLogController::class,'index']);
//     Route::post('upload',[UploadLogController::class,'upload']);
// });

Route::prefix('employee')->group(function(){
    Route::get('list',[EmployeeController::class,'list']);
    Route::get('find/{id}',[EmployeeController::class,'find']);
});

Route::prefix('deduction-type')->group(function(){
    Route::get('deduction-type-list',[DeductionTypesController::class,'deductionTypes']);
    Route::get('govt-type-list',[DeductionTypesController::class,'govtLoanTypes']);
});

Route::prefix('deduction')->group(function(){
    Route::get('list/{emp_id}/{dudection_type}',[DeductionsController::class,'list']);
    Route::get('table-headers',[DeductionsController::class,'tableHeaders']);

    Route::post('create',[DeductionsController::class,'create']);
    Route::post('update',[DeductionsController::class,'update']);
});

Route::prefix('govt-loan')->group(function(){
    Route::get('list/{emp_id}/{dudection_type}',[GovtLoanContoller::class,'list']);
    Route::get('table-headers',[GovtLoanContoller::class,'tableHeaders']);

    Route::post('create',[GovtLoanContoller::class,'create']);
    Route::post('update',[GovtLoanContoller::class,'update']);
});

Route::prefix('payroll-period')->group(function(){
    Route::get('list',[PayrollPeriodController::class,'list']);
    Route::get('unposted',[PayrollPeriodController::class,'unposted_list']);
    Route::get('posted',[PayrollPeriodController::class,'posted_list']);

});

Route::prefix('compensations')->group(function(){ 
    Route::get('list/{id}',[OtherIncomeController::class,'list']);
    Route::post('save',[OtherIncomeController::class,'save']);
    
});

Route::prefix('payroll-register')->group(function(){ 
    Route::prefix('unposted')->group(function(){ 
        Route::get('compute/{id}',[UnpostedController::class,'compute']);
        Route::get('excel/{id}',[UnpostedController::class,'excel']);
        Route::get('pdf/{id}',[UnpostedController::class,'pdf']);

        Route::post('post',[UnpostedController::class,'postPayroll']);

       
        
    });

    Route::prefix('posted')->group(function(){ 
        Route::get('computed/{id}',[PostedController::class,'computed']);
        // Route::post('unpost',[UnpostedController::class,'unpostPayroll']);
    });
    // Route::post('save',[OtherIncomeController::class,'save']);
    
});

Route::prefix('canteen-deduction')->group(function(){
    Route::get('list/{id}',[CanteenDeductionController::class,'list']);
    Route::post('save',[CanteenDeductionController::class,'save']);
});