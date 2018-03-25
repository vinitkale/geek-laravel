<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    protected $table = 'gm_banners';
    protected $primaryKey = 'title';
    protected $fillable = [
        'title','content'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
      protected $hidden = [           
    ];
      
      
      

}
