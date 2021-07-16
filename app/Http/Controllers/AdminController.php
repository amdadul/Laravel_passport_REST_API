<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function createAdmins(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'name' => 'required',
                'email' =>'required|unique:users',
                'password' => 'required|min:8'
            ],
            [
                'name.required' =>'Please Enter your name',
                'email.required' => 'Please enter phone no',
                'email.unique' => 'Given phone number already registered',
                'password.required' => 'please enter password',
                'password.min' => 'password must be greterthen or Equal to 8 charechter'
            ]
        );
        if($validator->fails())
        {
            return response()->json([
                "success" => false,
                "message" => $validator->getMessageBag()->first(),
                "error" => $validator->getMessageBag()
            ], 400);
        }

        $admin = new User();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);
        if($admin->save())
        {
            $token = $admin->createToken('tokenForAdmins')->accessToken;
            return response()->json([
                "success" => true,
                "message" => "Record created successfully",
                "error" => '',
                "api_token" => $token
            ], 201);
        }
        else
        {
            return response()->json([
                "success" => false,
                "message" => "Error Occur while creating a record",
                "error" => 'Error Occur while creating a record'
            ], 500);
        }
    }

    public function adminLogin(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'email' =>'required',
                'password' => 'required|min:8'
            ],
            [
                'email.required' => 'Please enter email',
                'password.required' => 'please enter password',
                'password.min' => 'password must be greterthen or Equal to 8 charechter'
            ]
        );
        if($validator->fails())
        {
            return response()->json([
                "success" => false,
                "message" => $validator->getMessageBag()->first(),
                "error" => $validator->getMessageBag()
            ], 400);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->guard('web')->attempt($credentials)) {
            $user =  auth()->guard('web')->user();
            $token = $user->createToken('tokenForAdmins')->accessToken;
            $admin = User::find($user->id);
            return response()->json([
                "success" => true,
                "message" => "Login successfully",
                "error" => '',
                "admin" => $admin,
                "api_token" => $token
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Email and password not match",
                "error" => 'Email and password not match',
            ], 401);
        }
    }

    public function adminLogout()
    {
        auth()->guard('api')->user()->token()->revoke();
        return response()->json([
            "success" => true,
            "message" => "Logout successfully",
            "error" => '',
        ], 200);
    }
}
