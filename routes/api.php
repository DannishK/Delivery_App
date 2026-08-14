<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\FleetController;
use App\Http\Controllers\FleetMemberController;
use App\Http\Controllers\RiderController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
     //BUSINESS ROUTES
    Route::post('/createBusiness', [BusinessController::class, 'createBusiness']);
    Route::get('/readAllBusinesses', [BusinessController::class, 'readAll']);
    Route::get('/readBusiness/{id}', [BusinessController::class, 'read']);
    Route::put('/updateBusiness/{id}', [BusinessController::class, 'update']);
    Route::delete('/deleteBusiness/{id}', [BusinessController::class, 'destroy']);


    //RIDER ROUTES
    Route::post('/createRiders', [RiderController::class, 'createRider']);
    Route::get('/readAllRiders', [RiderController::class, 'readAllRiders']);
    Route::get('/readRider/{id}', [RiderController::class, 'readRider']);
    Route::put('/updateRider/{id}', [RiderController::class, 'update']);
    Route::delete('/deleteRider/{id}', [RiderController::class, 'destroy']);

    //FLEET ROUTES
    Route::post('/createFleet', [FleetController::class, 'storeFleet']);
    Route::get('/readAllFleets', [FleetController::class, 'readAllFleets']);
    Route::get('/readFleet/{id}', [FleetController::class, 'readFleet']);
    Route::put('/updateFleet/{id}', [FleetController::class, 'update']);
    Route::delete('/deleteFleet/{id}', [FleetController::class, 'destroy']);

    //FLEET MEMBER ROUTES
    Route::post('/addRiderToFleet', [FleetMemberController::class, 'store']);
    Route::get('/readAllFleetMembers', [FleetMemberController::class, 'readAllFleetMembers']);
    Route::get('/readFleetMember/{id}', [FleetMemberController::class, 'readFleetMember']);
    Route::put('/updateFleetMember/{id}', [FleetMemberController::class, 'update']);
    Route::delete('/removeFleetMember/{id}', [FleetMemberController::class, 'destroy']);







    Route::post('/logout', [AuthController::class, 'logout']);

});