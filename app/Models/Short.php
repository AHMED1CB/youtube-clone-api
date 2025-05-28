<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    use HasFactory;
    use App\Traits\Viewable;
    use App\Traits\Reactable;
    use App\Traits\Commentable;

    protected $fillable = ['title' , 'descreption' , 'cover' , 'video' , 'duration' , 'slug' , 'channel'];


    public function channel()
    {
        return $this->belongsTo(User::class , 'channel');
    }
    
  


}
