<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Video;
use App\Services\Response;
use Illuminate\Support\Str;
use App\Services\VideoManager;
use App\Models\Reaction;

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

        $auth = request()->user->id;


        $reaction = Reaction::where('user_id' , $auth)->where('reactable_id' , $videoId)->first();

        $isReacted = false; 
        if ($reaction){

            $reaction->delete();
            $isReacted = false; 
            
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


}


