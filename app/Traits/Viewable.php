<?php

namespace App\Traits;

trait Viewable
{

    public function views()
    {
        return $this->morphMany(\App\Models\View::class, 'viewable');
    }
    
   


}
