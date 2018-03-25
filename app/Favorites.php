<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorites extends Model {
    protected $table = 'gm_events_favorites';
    protected $primaryKey = 'favorite_id';
    protected $fillable = [
        'favorite_id','event_id','user_id','favorite','updated_at','created_at'
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
