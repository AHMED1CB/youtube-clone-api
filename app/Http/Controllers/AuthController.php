<?php

namespace App\Http\Controllers;

use App\Services\Response;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\History;

class AuthController extends Controller
{
    
    public function getUserDetails(){
        $user = User::whereId(request()->user->id)->
                withCount(['subscribers' , 'subscriptions'  , 'shorts' , 'comments' , 'videos'])
                ->with([ 'subscribers' , 'subscriptions' , 'videos' => fn($q) => $q->withCount('views'),  'shorts'  ,'comments' , 'history.video' ] )
            ->first();


        return Response::push([
            'user' => $user 
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

    public function logoutUser(){
        
        // Delete All Tokens

        request()->user->tokens->each(fn($token) => $token->delete()); // Logout From All Devices

        return Response::push( [],  202 , 'Logout Success');

    }


    public function editUser(){

        $check = Validator::make(request()->all() , [
            'username' => ['unique:users'  , 'min:3' , 'max:255'],
            'name' =>     ['min:3' , 'max:255'],
            'profile_photo' => ['image' , 'max:5120', 'mimes:jpeg,png']
        ]);


        if ($check->fails()){

            return Response::push( [
                'errors' => $check->errors()
            ], 400 ,'Invalid Profile Data');
        
        }


        $keys = ['username' , 'name'];

        $finalData = [];

        foreach ($keys as $key ) {

            if (request()->has($key)){

                $finalData[$key] = request()->input($key);
            }

        }

        if (request()->hasFile('profile_photo')){
            $photoPath = request()->file('profile_photo')->store('users' , 'public');
            
            if ($photoPath){
                $finalData['profile_photo'] = $photoPath;
            }else{
                 return Response::push(status: 400 , message:'Invalid Profile Photo ');
            }

        }


        // Updating User Data;

        request()->user->update($finalData);

        return Response::push([
            'data' => $finalData
        ] , 200 , 'User Updated Success');


    }

}
