<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Reaction;
use App\Models\Video;
use App\Models\History;
use App\Services\Response;
use App\Services\VideoManager;
use App\Models\Comment;
use App\Models\Subscribe;
use App\Models\User;
use App\Models\View;

class VideoController extends Controller
{
    public function uplodVideo(){

        $check = Validator::make(request()->all() , [
            'title' => ['required' , 'min:3' , 'max:255'],
            'descreption' => ['required' , 'min:3'],
            'cover' => ['image' , 'max:5120' , 'mimes:jpeg,png'],
            'video' => [ 'required' , 'file', 
                        'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-flv,video/webm',
                        'max:102400'],

                        
        ]);



        if ($check->fails()){
            return Response::push([
                'errors' => $check->errors(),

            ] , 400 , 'Invalid Video Details');
        }
        $rand = Str::random(22);
        $file = request()->file('video');
        $fName = 'ytv-' . Str::replace( ['/' , '\\'] , '' , $rand) . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('videos' , $fName); 
    

        $videoModel = new Video(); 

        $videoModel->title = request()->title;
        $videoModel->descreption = request()->descreption;
        $videoModel->channel = request()->user->id;

        
        $duration = VideoManager::getDuration($fName);
        
        $slug = Str::slug((Str::random(12).request()->title));

        $cover = null;
        if (request()->hasFile('cover')){
           
            $cvn = ('cvs/'. time() . $slug  . '.png');
           
            $cvr = request()->file('cover')->storeAs('public' , $cvn);
            
            $cover = $cvn; 
        }else{
            $cover = VideoManager::generateThumbnail($fName , $slug);
        }
      


        $videoModel->slug = $slug;

        $videoModel->duration = $duration;

        $videoModel->cover = $cover;

        $videoModel->video = $fName;



        $videoModel->save();


        return Response::push([
            'video' => $videoModel,
            
        ] , 201 , 'Success');

    }

    
    public function reactVideo($videoId){

        $video = Video::findOrFail($videoId);

        $userId = request()->user->id;


        $reaction = $video->reactions()->where('user_id' , $userId)->where('reactable_id' , $videoId)->first();

        $isReacted = false; 
        if ($reaction){

            $reaction->delete();
            
        }else{
 
            $video->reactions()->save(
                new Reaction([
                'user_id' => $userId
            ])
        );
            $isReacted = true;
        }


        return Response::push(['is_reacted' => $isReacted , 'count_reacts' => $video->reactions()->count()   ] , 200,($isReacted ? 'React Created Success' : 'React Removed Sucess'));


    }


    public function getVideo($slug){
        
        $video = Video::with('comments' , 'channel')
                        ->withCount('views' , 'comments' , 'reactions')
                        ->where('slug' , $slug)
                        ->first();

        if(!$video){
            return Response::push([] , 404 , 'Video Not Found');
        }

          
        // Realated Vides
        $moreVideos  = Video::where('slug' , '!=' , $slug)
                          ->with('channel' , 'comments')
                          ->withCount('views' , 'reactions' , 'comments')
                          ->take(15)->get();


        $isSubscribed =    $video->getRelation('channel')
                            ->subscribers()
                            ->where('subscriber' , request()->user->id)
                            ->exists();

        $isReacted = $video->reactions()->where('user_id' , request()->user->id)->exists();
        
        $channel = $video->getRelation('channel');
        $channel->is_subscribed = $isSubscribed;




        $video->setRelation('channel' , $channel);

        $video->more_videos = $moreVideos; 
        $video->is_reacted = $isReacted; 

        $this->savedata($slug);

        return Response::push(['video' => $video ] , 200 , 'Success');

            




    }

    public function savedata($slug){

            // Adding To History (if History  Is able Recording) and add view

            $video = Video::where('slug' , $slug)->first();

            if($video && request()->user->historyState){ // check if recording is Active

                $isSaved = History::where('user' , request()->user->id)->where('video' , $video->id)->exists();
                
                if (!$isSaved){
                    $historyRecord = new History([
                        'user' => request()->user->id,
                        'video' => $video->id
                    ]);
                    
                    request()->user->history()->save($historyRecord);
                }

                $isWatched = $video->views()->where('viewer' , request()->user->id)->exists();
                if(!$isWatched){
                    $videoView = new View([
                        'viewer' => request()->user->id,
                    ]); 
                    
                    $video->views()->save($videoView);
                }

                return Response::push([] , 200, 'Video Details Added Success');

            }


    }


    public function commentOnVideo($videoId){

        $check = Validator::make(request()->only('comment') , [
            'comment' => ['required' ]
        ]);

        
        if ($check->fails()){
            
            return Response::push([
                'errors' => $check->errors()
            ] , 400 , 'Invalid Comment');

        }
        
        $video = Video::find($videoId);


        if($video){
            $comment =   new Comment([
                'comment' => request()->comment,
                'commentor' => request()->user->id 
            ]);
            $video->comments()->save(
              $comment
            );


            return Response::push([
                'comment' => $comment
                ] , 201 , 'Comment Created');


        }else{
            return Response::push([
                
            ] , 404 , 'Video Not Found');
        }


    }


    public function getVideos(){
        $videos = [];
        $count = 30;

        if (request()->has('count') && request()->count > 0 ){
            $count = request()->count;
        }
        
        $videos = Video::with([
                'reactions',
                'views',
                'channel',
                'comments'
        ])->withCount('views')->take($count)->get();
        

        return Response::push([
            'videos' => $videos,
        ] , 200 , 'Success');
    }


    public function deleteVideo($videoId){

        $video = request()->user->videos()->whereId($videoId)->first();

        if ($video){

            $video->delete();

            return Response::push([] , 200 , 'Video Deleted Success');
        }

        return Response::push([] , 404 , 'Video Not Found');


    }

}


