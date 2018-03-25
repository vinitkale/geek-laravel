<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model {
    protected $table = 'gm_event_rating';
    protected $primaryKey = 'rating_id';
    protected $fillable = [
        'rating_id','event_id','rating','user_id','updated_at','created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
      protected $hidden = [
            'created_at', 'updated_at',
    ];

      
      
}
