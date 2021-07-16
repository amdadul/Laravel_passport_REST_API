<?php

namespace App\Http\Controllers;

use App\Doctors;
use App\Patients;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;


class PatientsController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest:patient')->except('logout');
    }

    public function getAllPatients()
    {
        $patients = Patients::get()->toJson(JSON_PRETTY_PRINT);
        return response($patients, 200);
    }

    public function getAllDoctors(Request $request)
    {
        $doctors = Doctors::with('doctorDetails','doctorHolidays','doctorSpecialities','doctorQualifications')->get();
        return response()->json([
            "success" => true,
            "message" => 'Request successful',
            "error" => '',
            "doctors" => $doctors
        ], 200);
    }

    public function createPatients(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'name' => 'required',
                'phone_no' =>'required|min:11|unique:patients',
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

        $patients = new Patients();
        $patients->name = $request->name;
        $patients->email = $request->email;
        $patients->phone_no = $request->phone_no;
        $patients->password = bcrypt($request->password);
        if($patients->save())
        {
            $token = $patients->createToken('tokenForPatients')->accessToken;
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

        if (auth()->guard('patient')->attempt($credentials)) {
            $user =  auth()->guard('patient')->user();
            $tokenResult = $user->createToken('tokenForPatients');
            $token= $tokenResult->token;
            $token->expires_at = Carbon::now()->addWeeks(1);
            $patients = Patients::where('phone_no', '=', $request->phone_no)->first();
            return response()->json([
                "success" => true,
                "message" => "Login successfully",
                "error" => '',
                "patients" => $patients,
                "api_token" => $tokenResult->accessToken
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
        auth()->guard('patientapi')->user()->token()->revoke();
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
