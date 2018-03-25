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
use App\Organization;

class OrganizationController extends Controller {

 
    public function show($id) {
        $user = User::find($id);
        return view('organization.index', ['user_id' => $id, 'user' => $user]);
    }

    public function createOrganization($id) {
        $country = Country::all();
        $user = User::find($id);
        return view('organization.create', ['user_id' => $id, 'country' => $country,'user'=>$user]);
    }

    public function storeOrganization(Request $request) {
        if ($request->isMethod('post')) {
            $inputs = $request->All();
           
            $user_id = $inputs['user_id'];
            $rules = array(
                'organization_name' => 'required',
                'organization_description' => 'required',
                'user_id' => 'required'
            );

            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
               
                return redirect('organization/create/' . $user_id)
                                ->withErrors($validator, 'organization');
            } else {
                   
                if ($request->file('organization_logo') != NULL) {
                $imageTempName = $request->file('organization_logo')->getPathname();
                $imageName = str_random(10) . '_' . $request->file('organization_logo')->getClientOriginalName();
                $path = base_path() . '/uploads/user/';
                $request->file('organization_logo')->move($path, $imageName);
                $image_data = array(
                   'title' => $request->file('organization_logo')->getClientOriginalName(),
                   'name' => $imageName,
                   'type' => 'organization',
                   'extension' => $request->file('organization_logo')->getClientOriginalExtension(),
                   'content_type' => $request->file('organization_logo')->getClientMimeType()
                );
             $image = Image::create($image_data);
             $inputs['organization_logo'] = $image->image_id;
           }

                $res = Organization::create($inputs);
                if ($res != FALSE) {
                    return redirect('organization/' . $user_id)->with('success', 'Organization added successfully');
                } else {
                    return redirect('organization/' . $user_id)->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function editOrganization($organization_id) {
        $country = Country::all();
        $organization = Organization::find($organization_id);
        
        return view('organization.edit', ['organization_id' => $organization_id,'organization' => $organization,'country'=>$country]);
    }

    public function update(Request $request) {
        if ($request->isMethod('PUT')) {
            $inputs = $request->All();
            $organization_id = $request->input('organization_id');
            $user_id = $inputs['user_id'];
            $rules = array(
                'organization_name' => 'required',
                'organization_description' => 'required',
               
            );

            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
                $request->flash();
                return redirect('organization/edit/' . $organization_id)
                                ->withErrors($validator, 'organization');
            } else {
                  
                     if ($request->file('organization_logo') != NULL) {
                            $imageTempName = $request->file('organization_logo')->getPathname();
                            $imageName = str_random(10) . '_' . $request->file('organization_logo')->getClientOriginalName();
                            $path = base_path() . '/uploads/user/';
                            $request->file('organization_logo')->move($path, $imageName);
                            $image_data = array(
                               'title' => $request->file('organization_logo')->getClientOriginalName(),
                               'name' => $imageName,
                               'type' => 'organization',
                               'extension' => $request->file('organization_logo')->getClientOriginalExtension(),
                               'content_type' => $request->file('organization_logo')->getClientMimeType()
                            );
                          $image = Image::create($image_data);
                          $inputs['organization_logo'] = $image->image_id;
                          if($inputs['old_organization_logo']!=''){
                               $image_del = Image::find($inputs['old_organization_logo']);
                               $path = base_path() . '/uploads/user/';
                                if (!empty($image_del)) {
                                    if (file_exists($path . $image_del->name)) {
                                    unlink($path . $image_del->name);
                                    }
                                $image_del->delete();
                            }
                        } 
                     }else{
                         $inputs['organization_logo'] = $inputs['old_organization_logo']; 
                     }
                
                unset($inputs['_method']);
                unset($inputs['old_organization_logo']);
                unset($inputs['_token']);
                unset($inputs['organization_id']);
                $res = Organization::where('organization_id', $organization_id)->where('user_id', $user_id)
                        ->update($inputs);
                if ($res != FALSE) {
                    return redirect('organization/' . $user_id)->with('success', 'Organization updated successfully');
                } else {
                    return redirect('organization/' . $user_id)->with('error', 'some thing went wrong');
                }
            }
        }
    }

    public function delete($id, $user_id) {
        $organization = Organization::find($id);
        if($organization->organization_logo !=''){
              $image_del = Image::find($organization->organization_logo);
              $path = base_path() . '/uploads/user/';
                    if (!empty($image_del)) {
                        if (file_exists($path . $image_del->name)) {
                            unlink($path . $image_del->name);
                        }
                        $image_del->delete();
                    }
        }
        if ($organization->delete()) {
            return redirect('organization/' . $user_id)->with('success', 'Organization delete successfully');
        } else {
            return redirect('organization/' . $user_id)->with('error', 'some thing went wrong');
        }
    }

    public function getOrganizationData($user_id) {
//        if($user_id!=1){
        $organization = Organization::select(['organization_id','gm_organizations.user_id','users.username as username','organization_name','organization_location', 'organization_email', 'organization_website'])->leftJoin('users', 'gm_organizations.user_id', '=', 'users.user_id')->where('gm_organizations.user_id', $user_id);
//        }
//        else{
//        $organization = Organization::select(['organization_id','gm_organizations.user_id','users.username as username','organization_name','organization_location', 'organization_email', 'organization_website'])->leftJoin('users', 'gm_organizations.user_id', '=', 'users.user_id'); 
//        }


        return Datatables::of($organization)
                        ->addColumn('action', function ($organization) {
                            return '<a title="edit"  href="' . url('organization/edit/' . $organization->organization_id) . '" class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>&nbsp;&nbsp; <a title="delete" href="javascript:void(0)" data-href="' . url('organization/delete/' . $organization->organization_id . '/' . $organization->user_id) . '" class="btn btn-xs btn-danger delete" data-msg="organization"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
                        })
                        ->editColumn('username', function ($organization) {
                            return ucfirst($organization->username);
                        })
                        ->make(true);
    }

}
