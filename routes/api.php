<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TxnController;
use App\Http\Middleware\XSS;
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



Route::group(['middleware' => ['throttle:20,1','XSS']], function(){
    Route::post('user/register',[UserController::class,'create']);
    Route::post('user/login',[UserController::class,'login']);
});
Route::group(['middleware' => ['throttle:20,1','XSS','auth:api']], function(){
    Route::post('user/logout',[UserController::class,'logout']);
    Route::post('txn/create',[TxnController::class,'store']);
    Route::post('txn/edit/{id}',[TxnController::class,'update']);
    Route::post('txn',[TxnController::class,'index']);
    Route::post('txn/delete/{id}',[TxnController::class,'destroy']);
});
