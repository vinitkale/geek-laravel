<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Event;
use App\Blog;
use App\Location;
use App\Organization;
use App\Category;




class DashboardController extends Controller {

    public function __construct() {
       
      
    }

    public function index() {
       
    $data['total_event'] = Event::count();   
    $data['total_category'] = Category::count();   
    $data['total_blog'] = Blog::count();   
    $data['total_member'] = User::count();   
    $data['total_organization'] = Organization::count();   
    $data['total_venue'] = Location::count();   
    return view('dashboard',$data);
    }

}
