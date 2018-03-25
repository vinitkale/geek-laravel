<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model {
    protected $table = 'gm_states';
    protected $primaryKey = 'state_id';
    protected $fillable = [
        'state_id','name','country_id','updated_at','created_at'
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
