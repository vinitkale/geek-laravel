<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model {
    protected $table = 'gm_event_visit';
    protected $primaryKey = 'visit_id';
    protected $fillable = [
        'visit_id','event_id','visit_count','user_id','updated_at','created_at'
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
