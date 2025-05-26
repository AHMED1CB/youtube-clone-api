<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Response;
use Laravel\Sanctum\PersonalAccessToken;

class YoutubeAuth
{
    public function handle(Request $request, Closure $next){

        if (!auth('sanctum')->check()){

            return Response::push(message: 'Invalid or Expired Token' , status: 401 );
    
        }



        $request['user'] = auth('sanctum')->user();

        return $next($request);
    }
}
