<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/createBusiness', [BusinessController::class, 'createBusiness']);

    Route::get('/readAllBusinesses', [BusinessController::class, 'readAll']);

    Route::get('/readBusiness/{id}', [BusinessController::class, 'read']);

    Route::put('/updateBusiness/{id}', [BusinessController::class, 'update']);

    Route::delete('/deleteBusiness/{id}', [BusinessController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);

});