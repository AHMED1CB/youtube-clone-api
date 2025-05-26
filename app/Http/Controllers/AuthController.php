<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    
    public function getUserDetails(){

        return Response::push([
            'user' => request()->user, 
        ] , 200 , 'Success');

    }


    public function registerUser(){
        
            $check = Validator::make(request()->all() , [
                'name' => ['required' , 'min:3' , 'max:255'],
                'username' => ['required' , 'min:3' , 'max:255' , 'unique:users'],
                'email' => ['required' , 'unique:users' , 'email' , 'min:3' , 'max:255'],
                'password' => ['required' , 'min:8' , 'max:255'],
                
            ]);


            if ($check->fails()){

                return Response::push([
                    'errors' => $check->errors()
                ] , 400 , 'Invalid Register Data');

            }


        // Create A new User Record

        $user = User::create(request()->only('name' , 'username' , 'email' , 'password' ));
        
        if ($user){
            
            return Response::push(status: 201 , message:'User Registerd Success');

        }
        
        return Response::push(status: 400 , message:'Something Went Wrong');


    }


    function loginUser(){

        $check = Validator::make(request()->all() , [
            'email' => ['required' , 'exists:users' , 'email' , 'min:3' , 'max:255'],
            'password' => ['required' , 'min:8' , 'max:255'],
            
        ]);


        if ($check->fails() || !auth()->attempt(request()->only('email' , 'password'))){

            return Response::push(status: 401 , message:'Invalid Data or User Not Registerd');
        
        }

        $user = User::where('email' , request()->input('email'))->first();
        $token = $user->createToken('auth_token')->plainTextToken;

        return Response::push([
                'token' => $token 
            ] ,  200 , 'Login Success');
        

    }

    public function logoutUser(Request $request){
        
        // Delete All Tokens

        $request->user->tokens->each(fn($token) => $token->delete());

        return Response::push( [],  202 , 'Logout Success');

    }


}
