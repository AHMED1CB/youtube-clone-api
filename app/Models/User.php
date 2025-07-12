<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profile_photo'
    ];

   
    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function videos(){
        return $this->hasMany(Video::class , 'channel');
    }

    public function shorts(){
        return $this->hasMany(Short::class , 'channel');
    }


    public function subscribers(){
        return $this->hasMany(Subscribe::class , 'channel');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class , 'commentor');
    }


    public function subscriptions(){

        return $this->hasMany(Subscribe::class , 'subscriber');
        
    }
    


    public function history(){
        return $this->hasMany(History::class , 'user');
    }

}
