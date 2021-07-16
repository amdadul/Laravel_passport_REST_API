<?php

namespace App\Http\Controllers;

use App\Doctors;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

class DoctorsController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:doctor')->except('logout');
    }

    public function getAllDoctors()
    {
        $doctors = Doctors::with('doctorDetails','doctorHolidays','doctorSpecialities','doctorQualifications')->get();
        return response()->json([
            "success" => true,
            "message" => 'Request successful',
            "error" => '',
            "doctors" => $doctors
        ], 200);
    }

    public function createDoctors(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'name' => 'required',
                'phone_no' =>'required|min:11|unique:doctors',
                'password' => 'required|min:8'
            ],
            [
                'name.required' =>'Please Enter your name',
                'phone_no.required' => 'Please enter phone no',
                'phone_no.unique' => 'Given phone number already registered',
                'phone_no.min' => 'phone number must be 11 charechter',
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

        $patients = new Doctors();
        $patients->name = $request->name;
        $patients->email = $request->email;
        $patients->phone_no = $request->phone_no;
        $patients->password = bcrypt($request->password);
        if($patients->save())
        {
            $token = $patients->createToken('tokenForDoctors')->accessToken;
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

    public function login(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'phone_no' =>'required',
                'password' => 'required|min:8'
            ],
            [
                'phone_no.required' => 'Please enter phone no',
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
            'phone_no' => $request->phone_no,
            'password' => $request->password
        ];

        if (auth()->guard('doctor')->attempt($credentials)) {
            $user =  auth()->guard('doctor')->user();
            $token = $user->createToken('tokenForDoctors')->accessToken;
            $doctors = Doctors::find($user->id);
            return response()->json([
                "success" => true,
                "message" => "Login successfully",
                "error" => '',
                "doctors" => $doctors,
                "api_token" => $token
            ], 200);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Phone Number and password not match",
                "error" => 'Phone Number and password not match',
            ], 401);
        }

    }

    public function logout(Request $request)
    {
        auth()->guard('doctorapi')->user()->token()->revoke();
        return response()->json([
            "success" => true,
            "message" => "Logout successfully",
            "error" => '',
        ], 200);
    }


    protected function credentials(Request $request)
    {
        if(is_numeric($request->get('email'))){
            return ['phone_no'=>$request->get('email'),'password'=>$request->get('password')];
        }
        elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
            return ['email' => $request->get('email'), 'password'=>$request->get('password')];
        }
        return ['username' => $request->get('email'), 'password'=>$request->get('password')];
    }
}
