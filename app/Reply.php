<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model {
    protected $table = 'gm_review_reply';
    protected $primaryKey = 'review_reply_id';
    protected $fillable = [
        'review_reply_id','review_id','reply','reply_by','updated_at','created_at'
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
