<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model {
    protected $table = 'gm_countries';
    protected $primaryKey = 'id';
    protected $fillable = [
        'country_id','name','sortname','updated_at','created_at'
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
