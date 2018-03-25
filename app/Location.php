<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model {
    protected $table = 'gm_venue';
    protected $primaryKey = 'venue_id';
    protected $fillable = [
        'venue_id','city','state','address','user_id','country','longitude','latitude','venue_name','venue_description','venue_image','zipcode'
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
