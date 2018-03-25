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
use App\Location;
use Datatables;
use App\Organization;
class MemberController extends Controller {

  
    
    public function index() {
       
        return view('member.index');
    }

    public function create() {
        $country = Country::all();
        $category = Category::where('category_status', 1)->get();
        return view('member.create', ['country' => $country, 'category' => $category]);
    }

    public function store(Request $request) {
        if ($request->isMethod('post')) {
            $inputs = $request->All();
         
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|max:255|unique:users',
                'username' => 'required|max:255|unique:users',
                'dob' => 'required',
                'birth_place' => 'required',
                'country' => 'required',
                'state' => 'required',
                'city' => 'required',
                'zip_code' => 'required',
                'address' => 'required',
            );
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
                return redirect('member/create')
                                ->withErrors($validator, 'member');
            } else {
                
            if ($request->file('profile_image') != NULL) {
          
             $imageTempName = $request->file('profile_image')->getPathname();
             $imageName = str_random(10) . '_' . $request->file('profile_image')->getClientOriginalName();
             $path = base_path() . '/uploads/user/';
             $request->file('profile_image')->move($path, $imageName);
             $image_data = array(
                'title' => $request->file('profile_image')->getClientOriginalName(),
                'name' => $imageName,
                'type' => 'user',
                'extension' => $request->file('profile_image')->getClientOriginalExtension(),
                'content_type' => $request->file('profile_image')->getClientMimeType()
             );
            $image = Image::create($image_data);
            $inputs['profile_image'] = $image->image_id;
           }
                $inputs['phone'] = $inputs['contact_info'];
                $inputs['dob'] = date('Y-m-d', strtotime($inputs['dob']));
                $inputs['about_me'] = $inputs['about_me'];
                if(!empty($inputs['interest_topic'])){
                foreach($inputs['interest_topic'] as $cat){
                $cate_res = Category::where('category_name',$cat)->get();
               
               
                if($cate_res->isEmpty()){
                $category = new Category;
                $category->category_name = $cat;
                $category->category_status = 1;
                $category->save(); 
                }
                }
                $inputs['favorite_category'] = implode(',',$inputs['interest_topic']);    
                }
               
                $inputs['status'] = 'active';
                
               
                $res = User::create($inputs);
                if ($res != FALSE) {
                    return redirect('member')->with('success', 'User added successfully');
                } else {
                    return redirect('member')->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function edit($id) {
        $country = Country::all();
        $category = Category::where('category_status', 1)->get();
        $member = User::find($id);
        return view('member.edit', ['member' => $member,'country' => $country, 'category' => $category]);
    }
    
    public function show($id){
        $user_id = $id;
        $user =User::select(['first_name','last_name','email','address','gender','gender','dob','birth_place','about_me','zip_code','phone','favorite_category','gm_cities.name as city_name','gm_images.name as image'])->where('user_id',$id)->leftJoin('gm_cities', 'users.city', '=', 'gm_cities.city_id')->leftJoin('gm_images', 'users.profile_image', '=', 'gm_images.image_id')->get();
       
        if(!empty($user)){
            $user = $user[0];
        }
       
        return view('member.view',['user_id'=>$user_id,'user' => $user]); 
    }

    public function update(Request $request) {
        if (method_field('PUT')) {
            $inputs = $request->All();
            $user_id = $inputs['user_id'];
            $rules = array(
                'first_name' => 'required',
                'last_name' => 'required',
                'dob' => 'required',
                'birth_place' => 'required',
                'country' => 'required',
                'state' => 'required',
                'zip_code' => 'required',
                'address' => 'required',
                'user_id' => 'required',
            );
            
            if ($request->input('email') == $request->input('old_email')) {
                $rules['email'] = 'required|email|max:255';
            } else {
                $rules['email'] = 'required|email|max:255|unique:users';
            }
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                return redirect('member/'.$user_id.'/edit')
                                ->withErrors($validator, 'member');
            } else {
                
            if ($request->file('profile_image') != NULL) {
          
             $imageTempName = $request->file('profile_image')->getPathname();
             $imageName = str_random(10) . '_' . $request->file('profile_image')->getClientOriginalName();
             $path = base_path() . '/uploads/user/';
             $request->file('profile_image')->move($path, $imageName);
             $image_data = array(
                'title' => $request->file('profile_image')->getClientOriginalName(),
                'name' => $imageName,
                'type' => 'user',
                'extension' => $request->file('profile_image')->getClientOriginalExtension(),
                'content_type' => $request->file('profile_image')->getClientMimeType()
             );
            $image = Image::create($image_data);
            $inputs['profile_image'] = $image->image_id;
            if($inputs['old_profile']!=''){
              $image_del = Image::find($inputs['old_profile']);
              $path = base_path() . '/uploads/user/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
            }
           }else{
            $inputs['profile_image'] = $inputs['old_profile'];    
           }
                $inputs['phone'] = $inputs['contact_info'];
                $inputs['dob'] = date('Y-m-d', strtotime($inputs['dob']));
               if(!empty($inputs['interest_topic'])){
                foreach($inputs['interest_topic'] as $cat){
                $cate_res = Category::where('category_name',$cat)->get();
               
               
                if($cate_res->isEmpty()){
                $category = new Category;
                $category->category_name = $cat;
                $category->category_status = 1;
                $category->save(); 
                }
                }
                $inputs['favorite_category'] = implode(',',$inputs['interest_topic']);    
                }
                $inputs['status'] = 'active';
                unset($inputs['_method']);
                unset($inputs['_token']);
                unset($inputs['old_email']);
                unset($inputs['old_profile']);
                unset($inputs['interest_topic']);
                unset($inputs['contact_info']);
                $res = User::where('user_id', $user_id)
                        ->update($inputs);
                if ($res != FALSE) {
                    return redirect('member')->with('success', 'User updated successfully');
                } else {
                    return redirect('member')->with('error', 'some thing went wrong');
                }
            }   
        }
    }

    public function delete($id) {
        $user = User::find($id);
        Location::where('user_id',$id)->delete();
        Organization::where('user_id',$id)->delete();
        if($user->profile_image !=''){
              $image_del = Image::find($user->profile_image);
              $path = base_path() . '/uploads/user/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
        }
        if($user->delete()){
          return redirect('member')->with('success', 'Member delete successfully');   
        }else{
           return redirect('member')->with('error', 'some thing went wrong');  
        }
    }

    public function getMemberData() {

       $user = User::select(['user_id', 'first_name', 'last_name','username','email','address'])->where('user_id',"!=",1)->where('user_id',"!=",Auth::user()->user_id);

        return Datatables::of($user)
                        ->addColumn('action', function ($user) {
                            return '<a title="view" href="member/' . $user->user_id.'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-zoom-in"></i> View</a>&nbsp; <a title="organization" href="organization/' . $user->user_id.'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Organization</a>&nbsp;&nbsp; <a title="venue" href="location/' . $user->user_id.'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-plus"></i> Venue</a>&nbsp;<a title="edit" href="member/' . $user->user_id . '/edit" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp<a title="delete" href="javascript:void(0)" data-href="member/delete/' . $user->user_id . '" class="btn btn-xs btn-danger delete" data-msg="member"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->make(true);
    }

}
