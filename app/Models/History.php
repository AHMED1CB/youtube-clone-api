<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $table = 'history';

    public $guarded = [];

    public function video(){
        return $this->belongsTo(Video::class , 'video');
    }

    public function user(){
        return $this->belongsTo(User::class , 'user');
    }


}



