<?php

namespace App\Traits;

trait Viewable
{
    // Soon

    public function views()
    {
        return $this->morphMany(App\Models\View::class, 'viewable');
    }
}
