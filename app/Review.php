<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model {
    protected $table = 'gm_events_review';
    protected $primaryKey = 'event_review_id';
    protected $fillable = [
        'event_review_id','event_id','event_review','review_given_by','updated_at','created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
      protected $hidden = [
            'updated_at',
    ];

      
      
}
