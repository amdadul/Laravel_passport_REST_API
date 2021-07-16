<?php

namespace App\Http\Controllers;

use App\Doctors;
use App\DoctorSchedules;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorSchedulesController extends Controller
{
    public function days()
    {
        return response()->json([
            "success" => true,
            "message" => 'Request successful',
            "error" => '',
            "days" => DoctorSchedules::DAYS
        ], 200);
    }


    public function storeSingle(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'day' => 'required',
                'start_time' =>'required',
                'end_time' => 'required'
            ],
            [
                'day.required' =>'Please Enter a day of week',
                'start_time.required' => 'Please enter start time',
                'end_time.required' => 'please enter End time',
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

        $doctorId = auth()->guard('doctorapi')->user()->id;

        $check = DoctorSchedules::scheduleChecker($doctorId,$request->day,$request->start_time,$request->end_time);

        if($check)
        {
            return response()->json([
                "success" => false,
                "message" => "Schedule Already Taken",
                "error" => "Schedule Already Taken"
            ], 400);
        }

        $data = new DoctorSchedules();
        $data->day = $request->day;
        $data->doctor_id = $doctorId;
        $data->start_time = $request->start_time;
        $data->end_time = $request->end_time;
        $data->patient_limit = $request->patient_limit;
        $data->created_by = $doctorId;
        $data->created_by_type = User::DOCTOR;
        if($data->save())
        {
            return response()->json([
                "success" => true,
                "message" => "Record created successfully",
                "error" => '',
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

    public function storeArray(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'day' => 'required',
                'start_time' =>'required',
                'end_time' => 'required'
            ],
            [
                'day.required' =>'Please Enter a day of week',
                'start_time.required' => 'Please enter start time',
                'end_time.required' => 'please enter End time',
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

        $doctorId = auth()->guard('doctorapi')->user()->id;
        $i=0;
        $j=0;

        foreach ($request->day as $day) {
            $check = DoctorSchedules::scheduleChecker($doctorId, $day, $request->start_time[$i], $request->end_time[$i]);

            if ($check) {
                return response()->json([
                    "success" => false,
                    "message" => "Schedule Already Taken",
                    "error" => "Schedule Already Taken"
                ], 400);
            }
            $i++;
        }

        $save = true;

        foreach ($request->day as $day) {
            $data = new DoctorSchedules();
            $data->day = $day;
            $data->doctor_id = $doctorId;
            $data->start_time = $request->start_time[$j];
            $data->end_time = $request->end_time[$j];
            $data->patient_limit = $request->patient_limit[$j];
            $data->created_by = $doctorId;
            $data->created_by_type = User::DOCTOR;
            if($data->save())
            {

            }
            else
            {
                $save = false;
            }
            $j++;
        }

        if($save)
        {
            return response()->json([
                "success" => true,
                "message" => "Record created successfully",
                "error" => '',
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
}
