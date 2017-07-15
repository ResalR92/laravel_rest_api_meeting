<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class AuthController extends Controller
{
    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name' => 'required',
    		'email' => 'required|email',
    		'password' => 'required|min:5'
    	]);

    	$name = $request->input('name');
    	$email = $request->input('email');
    	$password = $request->input('password');

    	$user = new User([
    		'name' => $name,
    		'email' => $email,
    		'password' => bcrypt($password)
    	]);

    	//simpan ke DB
    	if($user->save()) {
    		$user->signin = [
    			'href' => 'api/v1/user/signin',
    			'method' => 'POST',
    			'params' => 'email,password'
    		];
    		$response = [
    			'msg' => 'User created',
    			'user' => $user
    		];

    		return response()->json($response, 201);
    	}

    	$response = [
    		'msg' => 'An error occurred'
    	];

    	return response()->json($response, 404);
    }
    //hasil
    // {
    //     "msg": "User created",
    //     "user": {
    //         "name": "Resal Ramdahadi",
    //         "email": "resalramdahadi92@gmail.com",
    //         "updated_at": "2017-07-15 13:47:12",
    //         "created_at": "2017-07-15 13:47:12",
    //         "id": 1,
    //         "signin": {
    //             "href": "api/v1/user/signin",
    //             "method": "POST",
    //             "params": "email,password"
    //         }
    //     }
    // }

    public function signin(Request $request)
    {
    	$this->validate($request, [
    		'email'=>'required|email',
    		'password' => 'required'
    	]);
    	
    	$credentials = $request->only('email','password');

    	try{
    		if(! $token = JWTAuth::attempt($credentials)) {
    			return response()->json(['msg'=>'Invalid credentials'],401);
    		}
    	} catch(JWTException $e) {
    		return response()->json(['msg'=>'Could not create token.'],500);
    	}

    	return response()->json(['token'=>$token], 200);
    }
    //hasil
    // {
    //     "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QvbGFyYXZlbC9sYXRpaGFuL0FQSS9yZXN0X2FwaS9wdWJsaWMvYXBpL3YxL3VzZXIvc2lnbmluIiwiaWF0IjoxNTAwMTMzMzQ2LCJleHAiOjE1MDAxMzY5NDYsIm5iZiI6MTUwMDEzMzM0NiwianRpIjoiMHRHdEE4VExKWDlRaDJaVyJ9.wI9a6fcEIK6LsBnp6lMwrla_OOI_4qCuZLubjvsMXqk"
    // }
}
