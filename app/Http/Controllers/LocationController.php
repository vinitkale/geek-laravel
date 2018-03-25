<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Country;
use App\State;
use App\City;
use App\Category;
use Auth;
use App\Image;
use Datatables;
use App\Location;

class LocationController extends Controller {

    public function show($id) {
        $user = User::find($id);
        return view('location.index', ['user_id' => $id, 'user' => $user]);
    }

    public function createLocation($id) {
        $country = Country::all();
        $user = User::find($id);
        return view('location.create', ['user_id' => $id, 'country' => $country,'user'=>$user]);
    }

    public function storeLocation(Request $request) {
        if ($request->isMethod('post')) {
            $inputs = $request->All();
          
            $user_id = $inputs['user_id'];
            $rules = array(
                'country' => 'required',
                'state' => 'required',
                'address' => 'required',
                'venue_name' => 'required'
            );

            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                if($inputs['venue_image']!=''){
                $this->delete_images($inputs['venue_image']);    
                }
                $request->flash();
                return redirect('location/create/' . $user_id)
                                ->withErrors($validator, 'location');
            } else {

                unset($inputs['image']);
                unset($inputs['_token']);
                $res = Location::create($inputs);
                if ($res != FALSE) {
                    return redirect('location/' . $user_id)->with('success', 'Location added successfully');
                } else {
                    return redirect('location/' . $user_id)->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function editLocation($location_id) {
        $country = Country::all();
        $location = Location::find($location_id);
       
            $attch_data = array();
            $attch_info = '';
            if ($location->venue_image != '') {
                $image_data = array();

                $images = explode(',', $location->venue_image );
                
                $attch_info = array();
                foreach ($images as $image_val) {
                    $images_array = Image::find($image_val);
//                    var_dump($images_array);
                                
                    if (!empty($images_array)) {

                        $path = base_path() . '/uploads/events/' . $images_array->name;
                          
                        if (file_exists($path)) {

                            $type = explode('/', $images_array->content_type);
                          
                            switch ($type[0]) {
                                case 'image':
                                    $src = asset('geekmeet/uploads/events/' . $images_array->name);
                                    $title = $images_array->title;
                                    $alt = $images_array->title;
                                    $attch_data[] = '<img img_id = "' . $images_array->image_id . '" width="200px" featured_image="' . $images_array->featured_image . '" height="200px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                                    $attch_info[] = array(
                                        'url' => url('delete_per_media'),
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;

                                case 'video':
                                    $src = asset('geekmeet/uploads/events/' . $images_array->name);
                                    $attch_data[] = '<video width="auto" height="160" controls> <source src="' . $src . '"></video>';
                                    $attch_info[] = array(
                                        'url' => url('delete_per_media'),
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;
                                case 'audio':
                                    $src = asset('geekmeet/uploads/events/' . $images_array->name);
                                    $attch_data[] = '<audio controls width="auto"> <source src="' . $src . '"></audio>';
                                    $attch_info[] = array(
                                        'url' => url('delete_per_media'),
                                        'caption' => $images_array->title,
                                        'key' => $images_array->image_id,
                                    );
                                    break;
                            }
                        }
                    }
                

                $location->images = $attch_data;
                $location->attachment = json_encode($attch_info);
            }
        }

        return view('location.edit', ['location_id' => $location_id, 'country' => $country, 'location' => $location]);
    }

    public function update(Request $request) {
        if ($request->isMethod('PUT')) {
            $inputs = $request->All();
           
            $location_id = $request->input('location_id');
      
            $user_id = $inputs['user_id'];
            $rules = array(
                'country' => 'required',
                'state' => 'required',
                'address' => 'required',
                'venue_name' => 'required'
            );

            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
                return redirect('location/edit/' . $location_id)
                                ->withErrors($validator, 'location');
            } else {
               

                unset($inputs['_method']);
                unset($inputs['_token']);
                unset($inputs['location_id']);
                unset($inputs['image']);
                $res = Location::where('venue_id', $location_id)->where('user_id', $user_id)
                        ->update($inputs);
                if ($res != FALSE) {
                    return redirect('location/' . $user_id)->with('success', 'Location updated successfully');
                } else {
                    return redirect('location/' . $user_id)->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function delete($id, $user_id) {
        $location = Location::find($id);
        
        if($location->venue_image!=''){
        $this->delete_images($location->venue_image);    
        }
        if ($location->delete()) {
            return redirect('location/' . $user_id)->with('success', 'Location delete successfully');
        } else {
            return redirect('location/' . $user_id)->with('error', 'some thing went wrong');
        }
    }

    public function getLocationData($user_id) {
//          if($user_id!=1){
        $location = Location::select(['venue_id','venue_name','gm_venue.user_id','gm_cities.name as city','users.username as username', 'gm_states.name as state', 'gm_countries.name as country', 'gm_venue.address'])->leftJoin('gm_cities', 'gm_venue.city', '=', 'gm_cities.city_id')->leftJoin('gm_states', 'gm_venue.state', '=', 'gm_states.state_id')->leftJoin('gm_countries', 'gm_venue.country', '=', 'gm_countries.id')->leftJoin('users', 'gm_venue.user_id', '=', 'users.user_id')->where('gm_venue.user_id', $user_id)->get();
//        }
//        else{
//         $location = Location::select(['venue_id','venue_name','gm_venue.user_id','gm_cities.name as city','users.username as username','gm_states.name as state', 'gm_countries.name as country', 'gm_venue.address'])->leftJoin('gm_cities', 'gm_venue.city', '=', 'gm_cities.city_id')->leftJoin('gm_states', 'gm_venue.state', '=', 'gm_states.state_id')->leftJoin('gm_countries', 'gm_venue.country', '=', 'gm_countries.id')->leftJoin('users', 'gm_venue.user_id', '=', 'users.user_id')->get();
//        }



        return Datatables::of($location)
                        ->addColumn('action', function ($location) {
                            return '<a title="edit"  href="' . url('location/edit/' . $location->venue_id) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;&nbsp; <a title="delete" href="javascript:void(0)" data-href="' . url('location/delete/' . $location->venue_id . '/' . $location->user_id) . '" class="btn btn-xs btn-danger delete" data-msg="venue"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                         ->editColumn('username', function ($organization) {
                            return ucfirst($organization->username);
                        })
                        ->make(true);
    }
    
    
       public function delete_images($image_id) {

        if ($image_id != '') {
            if (strpos($image_id, ',') > 0) {
                $ids = explode(',', $image_id);
                foreach ($ids as $val) {
                    $image_del = Image::find($val);
                    $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
                }
            } else {
                $image_del = Image::find($image_id);
                $path = base_path() . '/uploads/blog/';
                if (!empty($image_del)) {
                    if (file_exists($path . $image_del->name)) {
                        unlink($path . $image_del->name);
                    }
                    $image_del->delete();
                }
            }
            return true;
        }
    }

}
