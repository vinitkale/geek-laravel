<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'name', 'email', 'password','first_name','last_name','status','username','address','city','state','profile_image','phone','favorite_category','about_me','gender','birth_place','dob','zip_code','country','socail_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
            'remember_token',
    ];
    
       public function city(){
        return $this->hasOne('App\City','city_id', 'city');
    }
}
