<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('home', function () {
    return response()->json([ 'page' => 'home'] );
});


Route::prefix('auth')->group(function(){
    Route::post('/login',LoginController::class)->middleware('guest');
    Route::post('/logout',LogoutController::class)->middleware('auth:sanctum');
});

Route::get('test', function () {
    try{
        $y = 1 / 0;
    }catch(Exception $e){
        throw new Exception('divide 0');
    }
});
