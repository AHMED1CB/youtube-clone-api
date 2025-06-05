<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ShortsController;
use App\Http\Controllers\ChannelsController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\CommentsController;



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

    
Route::prefix('/videos')->controller(VideoController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/upload' , 'uplodVideo');
        
        Route::post('/{video}/react' , 'reactVideo');
        Route::post('/{video}/comment' , 'commentOnVideo');

        Route::post('/{slug}/savedata' , 'savedata');

    });


    Route::post('/{slug}' , 'getVideo');
    Route::post('/' , 'getVideos');



});




Route::prefix('/channels')->controller(ChannelsController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/{channel}/subscribe' , 'subscribeChannel');
        
    });

    Route::post('/{username}' , 'getChannelData');



});




Route::prefix('/shorts')->controller(ShortsController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/upload' , 'uplodShortVideo');
        
        Route::post('/{short}/react' , 'reactOnShortVideo');

        Route::post('/{short}/comment' , 'commentOnShortVideo');

        Route::post('/{slug}/savedata' , 'saveShortdata');

    });


    Route::post('/{slug}' , 'getShortVideo');
    
    Route::post('/' , 'getShortVideos');


});




Route::prefix('/history')->controller(HistoryController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/' , 'getHistoryVideos');
        
        Route::post('/changestate' , 'changestate');
        
        Route::post('/clear' , 'clearHistory');



    });



});




Route::prefix('/comments')->controller(CommentsController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/' , 'getAllUserComments');
        
        Route::post('/{comment}/delete' , 'deleteComment');
        
        Route::post('/{comment}/update' , 'updateComment');



    });



});



