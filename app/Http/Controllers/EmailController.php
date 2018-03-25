<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use App\Email;

class EmailController extends Controller {

    public function __construct() {
        
    }

    public function index() {
        $data['email'] = Email::get();
        return view('email.index', $data);
    }

    public function edit($id) {
        $email = Email::find($id);
        return view('email.edit', ['email' => $email]);
    }

    public function update(Request $request) {
       
        if (method_field('PUT')) {
            $inputs = $request->all();
            
            $rules = array(
                'subject' =>'required',
                'body' =>'required',
                'from_name' =>'required',
            );
             
            $email_id = $request->input('email_id');
            $validator = app('validator')->make($inputs, $rules);
            if ($validator->fails()) {
         
                return redirect("email/$email_id/edit")
                                ->withErrors($validator, 'email');
            } else {
                
                if ((array_key_exists('status', $inputs)) && ($inputs['status'] == 'on')) {
                    $inputs['status'] = 1;
                } else {
                    $inputs['status'] = 0;
                }
                
               unset($inputs['_method']);
               unset($inputs['email_id']);
                $res = Email::where('email_id', $email_id)
                        ->update($inputs);
               if($res){
                return redirect('email')->with('success', 'Email update successfully');
               }else{
                  return redirect('email')->with('error', 'some thing went wrong');    
               }
            }
        }else{
            return redirect('email')->with('error', 'some thing went wrong'); 
        }
    }

}
