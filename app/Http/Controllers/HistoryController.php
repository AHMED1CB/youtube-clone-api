<?php

namespace App\Http\Controllers;


use App\Services\Response;

class HistoryController extends Controller
{
    public function getHistoryVideos(){

        return Response::push([
            'history' => request()->user->history()
        ] ,  200 , 'Success');

    }


    public function changestate(){

        $currentState = request()->user->historyState;


        request()->user->historyState = !$currentState;
        
        request()->user->save();

        return Response::push(['currentState' => !$currentState] , 200 , 'State Changed Success');
    }


    public function clearHistory(){

        request()->user->history()->delete();

        return Response::push([] , 200 , 'Success');
    }
}
