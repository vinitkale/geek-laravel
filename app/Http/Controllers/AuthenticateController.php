<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\StoreResourceFailedException as store_exception;
use App\User;

class AuthenticateController extends Controller {

    public function index() {
        // TODO: show users
    }

    public function authenticate(Request $request) {
//        $credentials = $request->only('email', 'password');
        $credentials = json_decode(file_get_contents("php://input"), true);

        $rules = array(
            'email' => 'required|email',
            'password' => 'required'
        );
        $messages = ['required' => 'this field is required'];
        $validator = app('validator')->make($credentials, $rules, $messages);
        if ($validator->fails()) {
            throw new store_exception('Could not login.', $validator->errors());
        }
        try {

            if ($token = JWTAuth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                $user  = User::where('email', '=', $credentials['email'])->get();
                if($user[0]->status=="active"){
                return response()->json(['error' => '','message'=>'User Login Successfully','data'=>compact('token')], 200);
                }else{
                return response()->json(['error' => '','message' => 'Account is deactive'], 401);
                }
            }  else {
                return response()->json(['error' => '','message' => 'invalid_credentials or account is deactive'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // if no errors are encountered we can return a JWT
    }

    public function logout() {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        return response()->json(array('status' => 'success', 'message' => 'User logout successfully!'), 200);
    }

    public function login(Request $request) {
        if ($request->isMethod('post')) {
            $credentials = array(
                'username' => $request->input('username'),
                'password' => $request->input('password')
            );

            $rules = array(
                'username' => 'required',
                'password' => 'required'
            );
//            $messages = ['required' => 'this field is required'];
            $validator = app('validator')->make($credentials, $rules);
            if ($validator->fails()) {
                return redirect('/')
                                ->withErrors($validator, 'login');
            }
            else {

                if (Auth::attempt($credentials)) {
                 return redirect('/dashboard')->with('status','Login successfully');  
                } else {
                  return redirect('/')->with('status','Incorrect username or password');
                }
            } 

            // if no errors are encountered we can return a JWT
        } else {
            return view('login');
        }
    }

    public function doLogout() {
        Auth::logout(); // log the user out of our application
        return redirect('/')->with('success_status','User logout successfully');
    }

}
