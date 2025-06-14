<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscribe;
use App\Services\Response;

class ChannelsController extends Controller
{
    public function subscribeChannel($channelId){

        $channel = User::where('id' , '!=' , request()->user->id)->find($channelId);

        if ($channel){

            $subscribe = Subscribe::where('subscriber' , request()->user->id)->where('channel' , $channelId)->first();
            
            $isSubscribed = false;

            if ($subscribe){
                $subscribe->delete();
                $isSubscribed = false;
            }else{

                $subscribe = new Subscribe([
                    'channel' => $channelId,
                    'subscriber' => request()->user->id
                ]);

                request()->user->subscribes()->save($subscribe);

                $isSubscribed = true;


            }

            return Response::push([
                'is_subscribed' => $isSubscribed    
            ] , 200 , ($isSubscribed ? 'Subscribed Success' : 'Unsubscribed Success' ));

        }else{
            return Response::push([
                
            ] , 404 , 'Invalid Channel');
        }

    }


    public function getChannelData($username){

        $channel = User::where('username' , $username)->first();

        if ($channel){
    
            $data = User::where('username' , $username)->with([ 
                'subscribers' , 
                'videos' => ['views' , 'comments' , 'reactions'],
                'shorts' => ['views' , 'comments' , 'reactions']
              
                ])->first();
            return Response::push([
                'channel' => $data
            ] , 200 , 'Success');

        }
        
        return Response::push([
                
        ] , 404 , 'Channel Not Found');

    }
}
 