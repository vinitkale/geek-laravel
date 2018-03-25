<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model {
    protected $table = 'gm_blogs';
    protected $primaryKey = 'blog_id';
    protected $fillable = [
        'blog_id', 'blog_content','blog_title','thumb','added_by','status','created_at','published_date','blog_category','allow_comment','blog_media'
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
