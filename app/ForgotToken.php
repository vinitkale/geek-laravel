<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForgotToken extends Model {
    protected $table = 'gm_forgot_password';
    protected $primaryKey = 'forgot_id';
    protected $fillable = [
        'forgot_id','token','user_id','updated_at','created_at'
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
