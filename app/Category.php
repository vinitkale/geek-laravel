<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    protected $table = 'gm_event_category';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'category_name', 'category_status'
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
