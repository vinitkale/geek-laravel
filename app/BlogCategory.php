<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model {
    protected $table = 'gm_blog_category';
    protected $primaryKey = 'blog_category_id';
    protected $fillable = [
        'blog_category_name', 'blog_category_status'
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
