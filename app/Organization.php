<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model {
    protected $table = 'gm_organizations';
    protected $primaryKey = 'organization_id';
    protected $fillable = [
        'organization_id','user_id','organization_name','organization_description','organization_location','organization_email','organization_website','organization_contact','organization_logo','longitude','latitude','city','state','country'
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
