<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    protected $table = 'gm_event_attendance';
    protected $primaryKey = 'attendance_id';
    protected $fillable = [
        'attendance_id','event_id','user_id','created_at','updated_at'
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
