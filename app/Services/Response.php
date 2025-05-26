<?php

namespace App\Services;


class Response
{
    
    public static function push($data = [], $status = 400 , $message = 'Fail' ){

        return response()->json([
            'data' => $data,
            'statusCode' => $status,
            'message' => $message 
        ] , $status); 

    }

}
