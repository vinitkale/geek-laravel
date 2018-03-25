<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model {
    protected $table = 'gm_images';
    protected $primaryKey = 'image_id';
    protected $fillable = [
        'image_id','title','type','name','extension','content_type','featured_image','created_at','updated_at'
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
