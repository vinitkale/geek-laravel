<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model {
    protected $table = 'gm_email_management';
    protected $primaryKey = 'email_id';
    protected $fillable = [
        'email_id','email_type','status','subject','body','from_name','updated_at','created_at'
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
