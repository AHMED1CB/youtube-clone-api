<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
Route::controller(AuthController::class)->group(function (){

    
    Route::prefix('/auth')->group(function () {

            Route::middleware('auth.youtube')->group(function (){
                    
                Route::get('/' , 'getUserDetails');
                Route::post('/logout' , 'logoutUser');
                Route::post('/edit' , 'editUser');


            });

            Route::post('/register' , 'registerUser');

            Route::post('/login' , 'loginUser');


    });


});

