<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    public function commentor()
    {
        return $this->belongsTo(User::class , 'commentor');
    }

    
    protected $guarded = [];


}
