<?php

use Mahindra\Cc_auth\Controllers\AuthenticationController;
use Mahindra\Cc_auth\Middleware\AuthenticationMiddleware;
use Illuminate\Support\Facades\Route;

//middlewaregit 
Route::middleware([AuthenticationMiddleware::class])->group(function(){
    Route::get('php-sdk', [AuthenticationController::class,'checkValidation']); 
});