<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model {
    protected $table = 'gm_events';
    protected $primaryKey = 'event_id';
    protected $fillable = [
        'event_id','event_title','event_description','event_organize_by','start_date','end_date','start_time','end_time','website','country','state','city','location','purpose','category_id','contact_info','image_id','created_at','organizers','audience','updated_at','created_at'
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
