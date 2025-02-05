<?php

use App\Http\Controllers\V1\BrandController;
use App\Http\Controllers\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('v1.')->group(function () {

    Route::prefix('auth')->controller(\App\Http\Controllers\V1\Auth\AuthController::class)->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('logout', 'logout')->middleware('auth:api');
        Route::post('refresh', 'refresh')->middleware('auth:api');
        Route::post('send-otp', 'sendOtp');
        Route::post('verify-otp', 'verifyOtp');
    });

    Route::apiResource('brands',  BrandController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/{category}/children',[CategoryController::class,'children']);
    Route::get('categories/{category}/parent',[CategoryController::class,'parent']);
});
