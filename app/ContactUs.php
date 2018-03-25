<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model {
    protected $table = 'gm_contactus';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id','name','email','subject','message','created_at','updated_at'
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
