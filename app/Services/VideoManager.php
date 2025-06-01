<?php

namespace App\Services;
use FFMpeg;

class VideoManager{

    public static function generateThumbnail($path , $slug)
    {
            
        $cvrName = ('cvs/'. time() . $slug  . '.png'); 
        $cover = FFMpeg::fromDisk('videos')->open($path)
            ->getFrameFromSeconds(1)
            ->export()
            ->toDisk('public')
            ->save($cvrName);
            

            return $cvrName;
        

    }

    public static function getDuration($path)
    {
        $duration = FFMpeg::fromDisk('videos')->open($path)->getDurationInSeconds();
    


        if ($duration > 60){
            $minutes = int($duration / 60);
            $seconds = ($duration % 60);
            $duration = "$minutes:$seconds";
        }else{
            $duration = $duration . "s";
        }


        return $duration;

    }



}

