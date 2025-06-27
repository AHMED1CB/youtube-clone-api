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
        $fName = 'videos/ytv-' . Str::replace( ['/' , '\\'] , '' , $rand) . '.' . $file->getClientOriginalExtension();

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

        $auth = request()->user->id;


        $reaction = $video->reactions()->where('user_id' , $auth)->where('reactable_id' , $shortId)->first();

        $isReacted = false; 
        if ($reaction){

            $reaction->delete();
            
        }else{
 

            $video->reactions()->save(
                new Reaction([
                'user_id' => $auth
            ])
        );
            $isReacted = true;
        }


        return Response::push(['is_reacted' => $isReacted , 'count_reacts' => $video->reactions()->count()   ] , 200,($isReacted ? 'React Created Success' : 'React Removed Sucess'));


    }


    public function getVideo($slug){
        
        $video = Video::where('slug' , $slug)->first();

        if ($video){

            return Response::push([
                'video' => $video->with([
                    'reactions',
                    'views',
                    'channel',
                    'comments'
                ])->where('slug' , $slug)->first()
            ] , 200 , 'Success');

            

        }else{
            return Response::push([
                
            ] , 404 , 'Video Not Found');
        }



    }

    public function savedata($slug){

            // Adding To History and add view if user  Is Auth

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

                $videoView = new View([
                    'viewer' => request()->user->id,
                ]); 
                $video->views()->save($videoView);

                return Response::push([] , 200, 'Video Details Added Success');

            }else{

                return Response::push([] , 404, 'Video Not found');
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
        
        if (request()->has('count')){
            $videos = Video::with([
                'reactions',
                'views',
                'channel',
                'comments'
            ])->paginate(request()->count)->items();
        }else{
            $videos = Video::with([
                'reactions',
                'views',
                'channel',
                'comments'
            ])->take(50)->get();
        }

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


