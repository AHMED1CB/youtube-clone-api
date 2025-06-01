<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Str;
use FFMpeg;


class ProcessVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     */

    public function handle(): void {
    
        $path = $model->video;

        $video = FFMpeg::fromDisk('videos')->open($path);

        $duration = $video->getDurationInSeconds();

        if ($duration > 60){
            $minutes = int($duration / 60);
            $seconds = ($duration - ($minutes * 60));
            $duration = "$minutes:$seconds";
            $model->duration = $duration;
        }

        $slug = Str::slug($data['title']);
        $model->slug = $slug;

        $cover = $video->exportFramesByInterval(2)->toDisk('videos')->save(($slug . "vid-cov-" . $path));

        $model->cover = $cover;

        $model->save();

    }
}
