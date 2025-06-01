<?php

namespace App\Traits;

trait Reactable
{
      public function reactions()
      {
            return $this->morphMany(\App\Models\Reaction::class, 'reactable');
      }

      public function likes()
      {
         return $this->reactions()->where('type', 'like');
      }

      

      
}