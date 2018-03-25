<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
    protected $table = 'gm_cities';
    protected $primaryKey = 'city_id';
    protected $fillable = [
        'city_id','name','state_id','updated_at','created_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
      protected $hidden = [
            'created_at', 'updated_at',
    ];
      
        public function user()
    {
        return $this->belongsTo('App\User','city'); 
    } 
      
        

}
