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

    
Route::prefix('/videos')->middleware('auth.youtube')->controller(VideoController::class)->group(function () {

            
        Route::post('/upload' , 'uplodVideo');
        
        Route::post('/{video}/react' , 'reactVideo');

        Route::post('/{video}/comment' , 'commentOnVideo');

        Route::delete('/{video}/delete' , 'deleteVideo');

        Route::get('/{slug}' , 'getVideo');

        Route::get('/' , 'getVideos');





});




Route::prefix('/channels')->middleware('auth.youtube')->controller(ChannelsController::class)->group(function () {

            
        Route::post('/{channel}/subscribe' , 'subscribeChannel');
        
        Route::get('/{username}' , 'getChannelData');
    




});




Route::prefix('/shorts')->middleware('auth.youtube')->controller(ShortsController::class)->group(function () {

 
        Route::post('/upload' , 'uplodShortVideo');

        Route::delete('/{short}/delete' , 'deleteShort');

        Route::post('/{short}/react' , 'reactOnShortVideo');

        Route::post('/{short}/comment' , 'commentOnShortVideo');


        Route::get('/{slug}' , 'getShortVideo');
        
        Route::get('/' , 'getShortVideos');



});




Route::prefix('/history')->middleware('auth.youtube')->controller(HistoryController::class)->group(function () {
        
        Route::post('/changestate' , 'changestate');
        
        Route::post('/clear' , 'clearHistory');


});




Route::prefix('/comments')->middleware('auth.youtube')->controller(CommentsController::class)->group(function () {

        Route::get('/' , 'getAllUserComments');
        
        Route::delete('/{comment}/delete' , 'deleteComment');
        
        Route::post('/{comment}/update' , 'updateComment');

});
