<?php

use Illuminate\Support\Facades\Route,
 App\Http\Controllers\AuthController,
 App\Http\Controllers\VideoController,
 App\Http\Controllers\ShortsController,
 App\Http\Controllers\ChannelsController,
 App\Http\Controllers\HistoryController,
 App\Http\Controllers\CommentsController,
Illuminate\Support\Facades\Storage;



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

        Route::post('/{video}/delete' , 'deleteVideo');

        Route::post('/{slug}/savedata' , 'savedata');

    });


    Route::post('/{slug}' , 'getVideo');
    Route::post('/' , 'getVideos');



});




Route::prefix('/channels')->controller(ChannelsController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/{channel}/subscribe' , 'subscribeChannel');
        
        Route::post('/{username}' , 'getChannelData');
    
    });




});




Route::prefix('/shorts')->controller(ShortsController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
            
        Route::post('/upload' , 'uplodShortVideo');

        Route::post('/{short}/delete' , 'deleteShort');

        Route::post('/{short}/react' , 'reactOnShortVideo');

        Route::post('/{short}/comment' , 'commentOnShortVideo');

        Route::post('/{slug}/savedata' , 'saveShortdata');

    });


    Route::post('/{slug}' , 'getShortVideo');
    
    Route::post('/' , 'getShortVideos');

});




Route::prefix('/history')->controller(HistoryController::class)->group(function () {

    Route::middleware('auth.youtube')->group(function (){
        
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
