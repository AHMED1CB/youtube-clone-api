<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Viewable;
use App\Traits\Reactable;
use App\Traits\Commentable;
class Short extends Model
{
    use HasFactory;
    use Viewable, Reactable , Commentable;

    protected $fillable = ['title' , 'descreption' , 'cover' , 'video' , 'duration' , 'slug' , 'channel'];


    public function channel()
    {
        return $this->belongsTo(User::class , 'channel');
    }
    
    public function deleteShort($videoId){

        $video = request()->user->videos()->whereId($videoId)->first();

        if ($video){

            $video->delete();

            return Response::push([] , 200 , 'Video Deleted Success');
        }

        return Response::push([] , 404 , 'Video Not Found');


    }
    
  


}
