<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Viewable;
use App\Traits\Reactable;
use App\Traits\Commentable;

class Video extends Model
{
    use HasFactory;
    use Viewable;
    use Reactable;
    use Commentable;

    protected $fillable = ['title' , 'descreption' , 'cover' , 'video' , 'duration' , 'slug' , 'channel'];
    protected $appends = ['creation_date'];
    protected $hidden = ['created_at' , 'updated_at'];

    
    public function channel()
    {
        return $this->belongsTo(User::class , 'channel_id');
        
    }

    public function getCreationDateAttribute(){
        return $this->created_at->diffForHumans();
    }



 
    

}
