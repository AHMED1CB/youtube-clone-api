<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\Response;
use App\Services\VideoManager;

use App\Models\Short;
use App\Models\Comment;
use App\Models\Reaction;
use App\Models\View;
use App\Models\History;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class ShortsController extends Controller
{
    

    public function uplodShortVideo(){

            
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
                
            ] , 400 , 'Invalid Short Video Details');
        }
        
        $rand = Str::random(22);
        
        $file = request()->file('video');

        $fName = 'yts-' . Str::replace( ['/' , '\\'] , '' , $rand) . '.' . $file->getClientOriginalExtension();

        
        $path = $file->storeAs('videos' , $fName); // Shorts Will be in Videos Disk Too
        
        if (VideoManager::durationInSeconds($fName) > 2){
            return Response::push([] , 400 , 'Video is Too Long');
        }

        $videoModel = new Short(); 

        $videoModel->title = request()->title;
        $videoModel->descreption = request()->descreption;
        $videoModel->channel_id = request()->user->id;

        
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
        ] , 200 , 'Uploaded Success');

    }


    public function reactOnShortVideo($shortId){

        
        $short = Short::findOrFail($shortId);

        $auth = request()->user->id;


        
        $reaction = $short->reactions()->where('user_id' , $auth)->where('reactable_id' , $shortId)->first();
        $isReacted = false; 

        if ($reaction){

            $reaction->delete();
            
        }else{
            $short->reactions()->save(
                new Reaction([
                'user_id' => $auth
            ])
        );
            $isReacted = true;
        }


        return Response::push(['is_reacted' => $isReacted  ,  'count_reacts' => $short->reactions()->count()   ] , 200,($isReacted ? 'React Created Success' : 'React Removed Sucess'));


    }


    public function commentOnShortVideo($shortId){

        
        $check = Validator::make(request()->only('comment') , [
            'comment' => ['required' ]
        ]);

        
        if ($check->fails()){
            
            return Response::push([
                'errors' => $check->errors()
            ] , 400 , 'Invalid Comment');

        }
        
        $short = Short::find($shortId);


        if($short){
            $comment =   new Comment([
                'comment' => request()->comment,
                'commentor' => request()->user->id 
            ]);
            $short->comments()->save(
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

    public function saveShortdata($slug){
            
        // Adding View To Video

            $short = Short::where('slug' , $slug)->first();

            $isSaved = $short->views()->where('viewer' , request()->user->id)->exists();
            
            if (!$isSaved){

                $shortView = new View([
                    'viewer' => request()->user->id,
                ]); 
                $short->views()->save($shortView);
            }





    }

    public function getShortVideo($slug){

        
        $short = Short::where('slug' , $slug)->exists();

        if (!$short){
             return Response::push([
                
            ] , 404 , 'Video Not Found');
        
        }



            $this->saveShortdata($slug);

            $data = Short::with([
                    'reactions',
                    'views',
                    'channel',
                    'comments'
                ])->withCount('comments' , 'reactions' , 'views')
                 ->where('slug' , $slug)->first();


            $isSubscribed = $data->channel->subscribers()
                                            ->where('subscriber' , request()->user->id)
                                            ->exists();


            $data->channel->is_subscribed = $isSubscribed;
            $data->is_reacted = $data->reactions()->where('user_id' , request()->user->id)->exists();



            return Response::push([
                'video' => $data
            ] , 200 , 'Success');

            

      



    }

    public function getShortVideos(){

        $shorts = [];
        $count = 50;

        if (request()->has('count') && request()->count > 0 ){
            $count = request()->count;
        }


        $shorts = Short::with([
                'reactions',
                'views',
                'channel',
                'comments',
                'channel.subscribers' => fn($q) => $q->where('subscriber' , request()->user->id),
                'reactions' => fn($q) => $q->where('user_id' , request()->user->id)
            ])->withCount('views' , 'reactions' , 'comments')->take($count)->get();
        
        // Adding is_subscribed And Add View

        $shorts->each(function($short) {

            $short->channel->is_subscribed = $short->channel->subscribers->isNotEmpty();
            $short->is_reacted = $short->reactions->isNotEmpty();

            $this->saveShortdata($short->slug);

            return $short;
        });

        return Response::push([
            'videos' => $shorts,
        ] , 200 , 'Success');
    

    }


    
    public function deleteShort($shortId){

        $short = request()->user->shorts()->whereId($shortId)->first();

        if ($short){

            $short->delete();

            return Response::push([] , 200 , 'short Deleted Success');
        }

        return Response::push([] , 404 , 'short Not Found');

    }

}
