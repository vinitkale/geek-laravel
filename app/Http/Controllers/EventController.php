<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Image;
use App\Event;
use Datatables;
use App\Country;
use App\State;
use App\City;
use App\Category;
use App\User;
use App\Location;
use App\Organization;
use Auth;

class EventController extends Controller {

    public function index() {

        return view('event.index');
    }

    public function create() {
        $country = Country::all();
        $category = Category::where('category_status', 1)->orderBy('category_id', 'desc')->get();
        $location = Location::where('user_id',Auth::user()->user_id)->orderBy('venue_id', 'desc')->get();
      
        $organization = Organization::where('user_id',Auth::user()->user_id)->orderBy('organization_id', 'desc')->get();
        return view('event.create', ['country' => $country, 'category' => $category,'organization'=>$organization,'location'=>$location]);
    }

    public function store(Request $request) {
        if ($request->isMethod('post')) {

            $inputs = $request->All();

            unset($inputs['image']);


            $rules = array(
                'event_title' => 'required|max:255',
                'event_description' => 'required',
                'start_date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'website' => 'required',
                'location' => 'required',
            );
            if ($request->input('start_date') != date('d-m-Y', time())) {
                $rules['end_date'] = "required";
            }
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
                return redirect('event/create')
                                ->withErrors($validator, 'event');
            } else {
                if(!empty($inputs['keyword'])){
                foreach($inputs['keyword'] as $cat){
                $cate_res = Category::where('category_name',$cat)->get();
               
               
                if($cate_res->isEmpty()){
                $category = new Category;
                $category->category_name = $cat;
                $category->category_status = 1;
                $category->save(); 
                }
                }
                $inputs['category_id'] = implode(',',$inputs['keyword']);    
                }
                $inputs['event_organize_by'] = Auth::user()->user_id;  
               
                if(!empty($inputs['audience'])){
                $inputs['audience'] = implode(',', $request->input('audience'));
                }else{
                $inputs['audience'] = '';    
                }
                
                $inputs['start_date'] = date('Y-m-d', strtotime($request->input('start_date')));
                if ($request->input('start_date') != date('d-m-Y', time())) {
                    $inputs['end_date'] = date('Y-m-d', strtotime($request->input('end_date')));
                } else {
                    $inputs['end_date'] = date('Y-m-d', time());
                }
                $res = Event::create($inputs);
                if ($res != FALSE) {
                    return redirect('event')->with('success', 'Event added successfully');
                } else {
                    return redirect('event')->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function edit($id) {
        $event = Event::select(['*','gm_venue.address as location_address','gm_venue.venue_id as location_id'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->where('event_id',$id)->get();
        if(!empty($event)){
        $country = Country::all();
        
        $category = Category::where('category_status', 1)->orderBy('category_id', 'desc')->get();
        $organization = Organization::where('user_id',Auth::user()->user_id)->orderBy('organization_id', 'desc')->get();
          $location = Location::where('user_id',Auth::user()->user_id)->orderBy('venue_id', 'desc')->get();
        $event = $event[0];
        if ($event->image_id != '') {
            $image_data = array();
            $images = explode(',', $event->image_id);
            $attch_info = array();
            foreach ($images as $image_val) {
                $images_array = Image::find($image_val);
                if (!empty($images_array)) {
                    $src = asset('geekmeet/uploads/events/' . $images_array->name);
                    $title = $images_array->title;
                    $alt = $images_array->title;
                    $image_data[] = '<img img_id = "'.$images_array->image_id.'" width="200px" featured_image="'.$images_array->featured_image.'" height="200px" src="' . $src . '" class="img-responsive" alt="' . $alt . '" title="' . $title . '">';
                    $attch_info[] = array(
                        'url'=> url('delete_per_image'),
                        'caption' => $title,
                        'key' => $images_array->image_id,
                    );
                }
            }


            $event->images = $image_data;
            $event->attachment = json_encode($attch_info);
        } 


        return view('event.edit', ['event' => $event, 'country' => $country, 'category' => $category,'organization'=>$organization,'location'=>$location]);
    }
    }

    public function update(Request $request) {
        if ($request->isMethod('PUT')) {
           
            $inputs = $request->All();
            $event_id = $inputs['event_id'];
            unset($inputs['image']);


            $rules = array(
                'event_title' => 'required|max:255',
                'event_description' => 'required',
                'start_date' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'website' => 'required',
                'location' => 'required',
            );
            if ($request->input('start_date') != date('d-m-Y', time())) {
                $rules['end_date'] = "required";
            }
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
                return redirect("event/$event_id/edit")
                                ->withErrors($validator, 'event');
            } else {

                if(!empty($inputs['keyword'])){
                foreach($inputs['keyword'] as $cat){
                $cate_res = Category::where('category_name',$cat)->get();
               
               
                if($cate_res->isEmpty()){
                $category = new Category;
                $category->category_name = $cat;
                $category->category_status = 1;
                $category->save(); 
                }
                }
                $inputs['category_id'] = implode(',',$inputs['keyword']);    
                }else{
                $inputs['category_id'] = '';    
                    
                }
                if(!empty($inputs['audience'])){
                $inputs['audience'] = implode(',', $request->input('audience'));
                }else{
                $inputs['audience'] = '';    
                }
                $inputs['start_date'] = date('Y-m-d', strtotime($request->input('start_date')));
                if ($request->input('start_date') != date('d-m-Y', time())) {
                    $inputs['end_date'] = date('Y-m-d', strtotime($request->input('end_date')));
                } else {
                    $inputs['end_date'] = date('Y-m-d', time());
                }

              
                unset($inputs['keyword']);
                unset($inputs['_method']);
                unset($inputs['_token']);
                unset($inputs['old_image_id']);


                Event::where('event_id', $event_id)
                        ->update($inputs);

                return redirect('event')->with('success', 'Event updated successfully');
            }
        }
    }

    public function getEventData() {


        $event = Event::select(['event_id', 'event_title', 'event_description', 'start_date', 'end_date', 'start_time', 'end_time', 'gm_venue.address as location', 'category_id','users.username','gm_organizations.organization_name as org_name'])->leftJoin('gm_venue', 'gm_events.location', '=', 'gm_venue.venue_id')->leftJoin('users', 'gm_events.event_organize_by', '=', 'users.user_id')->leftJoin('gm_organizations', 'gm_events.organizers', '=', 'gm_organizations.organization_id')->get();
        
        return Datatables::of($event)
                        ->addColumn('action', function ($event) {
                            return '<a title="view" href="event/' . $event->event_id . '/edit" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-zoom-in"></i> View</a>&nbsp;&nbsp; <a title="edit"  href="event/' . $event->event_id . '/edit" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;&nbsp; <a title="delete" href="javascript:void(0)" data-href="event/delete/' . $event->event_id . '" class="btn btn-xs btn-danger delete" data-msg="event"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->editColumn('start_date', function ($event) {
                            $date_obj = date_create($event->start_date);
                            return date_format($date_obj, 'F j, Y');
                        })
                        ->editColumn('end_date', function ($event) {
                            $date_obj = date_create($event->end_date);
                            return date_format($date_obj, 'F j, Y');
                        })
                        
                        ->editColumn('org_name', function ($event) {
                           if($event->org_name!=''){
                               return $event->org_name;
                           }else{
                               return $event->username;
                           }
                        })
                        ->make(true);
    }

    public function getState(Request $request) {

        $country_id = $request->input('country_id');
        $state = State::where('country_id', $country_id)->get()->toArray();
        if (!empty($state)) {
            echo json_encode($state);
        } else {
            echo FALSE;
        }
        die;
    }

    public function getCity(Request $request) {

        $state_id = $request->input('state_id');
        $city = City::where('state_id', $state_id)->get()->toArray();
        if (!empty($city)) {
            echo json_encode($city);
        } else {
            echo FALSE;
        }
        die;
    }
    
    
    public function getVenue(Request $request) {

        $organizer_id = $request->input('organizer_id');
        $city = Location::where('user_id', $organizer_id)->orderBy('venue_id', 'desc')->get()->toArray();
        if (!empty($city)) {
            echo json_encode($city);
        } else {
            echo FALSE;
        }
        die;
    }
    
    public function getOrganization(Request $request) {

        $organizer_id = $request->input('organizer_id');
        $organization = Organization::where('user_id', $organizer_id)->orderBy('organization_id', 'desc')->get()->toArray();
        if (!empty($organization)) {
            echo json_encode($organization);
        } else {
            echo FALSE;
        }
        die;
    }

    public function upload_image(Request $request) {
        
        if ($request->file('image') != NULL) {
          
            $imageTempName = $request->file('image')->getPathname();
           
            $imageName = str_random(10) . '_' . $request->file('image')->getClientOriginalName();
            $path = base_path() . '/uploads/events/';
            $request->file('image')->move($path, $imageName);
            $image_data = array(
                'title' => $request->file('image')->getClientOriginalName(),
                'name' => $imageName,
                'type' => 'event',
                'extension' => $request->file('image')->getClientOriginalExtension(),
                'content_type' => $request->file('image')->getClientMimeType()
            );

            $image = Image::create($image_data);
            echo $image->image_id;
            die;
        }
      }
      
      
    public function delete_image(Request $request) {
        
        if ($request->input('id') != NULL) {
          if(strpos($request->input('id'),',')>0){
             $ids = explode(',',$request->input('id'));
             foreach($ids as $val){
               $image_del = Image::find($val);
              $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }   
             }
          }else{
          $image_del = Image::find($request->input('id'));
          $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
          }
                    echo json_encode(array("status"=>1));
           
        }
      }
      
    public function featured_image(Request $request) {
         if ($request->input('id') != NULL) {
            $status = $request->input('status');
            Image::where('image_id', $request->input('id'))
                        ->update(array('featured_image'=>$status));
          }
                    echo json_encode(array("status"=>$status));  
       
      }
      
    public function delete_per_image(Request $request) {
  
        if ($request->input('key') != NULL) {
         
          $image_del = Image::find($request->input('key'));
          $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
          }
                    echo json_encode(array("status"=>1));
           
        }
        
        
     public function delete($id) {
        $event = Event::find($id);
        if ($event->image_id != '') {
          if(strpos($event->image_id,',')>0){
             $ids = explode(',',$event->image_id);
             foreach($ids as $val){
               $image_del = Image::find($val);
              $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }   
             }
          }else{
          $image_del = Image::find($event->image_id);
          $path = base_path() . '/uploads/events/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
          }
                    echo json_encode(array("status"=>1));
           
        }
        
        if($event->delete()){
          return redirect('event')->with('success', 'Event delete successfully');   
        }else{
           return redirect('event')->with('error', 'some thing went wrong');  
        }
    } 
    
    
    public function addLocation(Request $request){
      if ($request->isMethod('post')) {
            $inputs = $request->All();
            
            $user_id = Auth::user()->user_id;
            $rules = array(
                'country' => 'required',
                'state' => 'required',
                'address' => 'required',
                'venue_name' => 'required'
            );

            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $response_data = array(
                'status'=>false,
                'data'=>$validator->errors()    
                );
                
                echo json_encode($response_data);
                die;
            } else {
               
                    $images = $request->file('location_image');
                    foreach ($images as $key=>$val){
                        if($val==''){
                            unset($images[$key]);
                        }
                    }
                    
                    if(!empty($images)){
                    foreach($images as $val){
                    $imageTempName = $val->getPathname();
                    $imageName = str_random(10) . '_' . $val->getClientOriginalName();
                    $path = base_path() . '/uploads/events/';
                    $val->move($path, $imageName);
                    $image_data = array(
                        'title' => $val->getClientOriginalName(),
                        'name' => $imageName,
                        'type' => 'blog',
                        'extension' => $val->getClientOriginalExtension(),
                        'content_type' => $val->getClientMimeType()
                    );
                    $image = Image::create($image_data);
                    $img[] =$image->image_id; 
                    }
                    $inputs['venue_image'] = implode(',',$img);
                    }
                unset($inputs['request_user_id']);
                unset($inputs['location_image']);
                $inputs['user_id'] = $user_id;
               
                
         
                $res = Location::create($inputs);
                if ($res != FALSE) {
                      $response_data = array(
                      'status'=>TRUE,
                      'data'=>array(
                       'location_id'=>$res->venue_id,   
                       'location_name'=>$res->venue_name,   
                       )    
                     );
                 echo json_encode($response_data);
                 die;
                } else {
                      $response_data = array(
                      'status'=>FALSE,
                      'msg'=>"some thing went wrong"
                     );
                       echo json_encode($response_data);
                       die;
                }
            }
        }
    }
      

}
