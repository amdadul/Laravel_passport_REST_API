<?php

namespace App\Http\Controllers;

use App\DoctorDetails;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorDetailsController extends Controller
{
    public function updateProfile(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'full_name' => 'required',
                'title' => 'required',
                'picture' => 'mimes:jpg,jpeg,png|max:1000',
            ],
            [
                'full_name.required' =>'Please Enter your name',
                'title.required' =>'Please Enter your Specialities Title',
                'picture.mimes' => 'Only jpg and png format acceptable',
                'picture.max' => 'Image Size too large',
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

        if(DoctorDetails::hasDetails($doctorId))
        {
            $data = DoctorDetails::where("doctor_id","=",$doctorId);

            $data->updated_by = $doctorId;
            $data->updated_by_type = "Doctor";
        }
        else
        {
            $data = new DoctorDetails();

            $data->created_by = $doctorId;
            $data->created_by_type = User::DOCTOR;
        }

        $data->doctor_id = $doctorId;
        $data->full_name = $request->full_name;
        $data->title = $request->title;
        $data->experience = $request->experience;
        $data->biography = $request->biography;

        if ($request->hasFile('picture')) {
            $file = $request->file('picture');
            $path = 'public/uploads/doctors/profile';
            $file_name = time() . rand(00, 99) . '.' . $file->getClientOriginalName();
            $file->move($path, $file_name);
            $data->picture = $path . '/' . $file_name;
        }

        if($data->save())
        {
            return response()->json([
                "success" => true,
                "message" => "Record Updated successfully",
                "error" => '',
            ], 201);
        }
        else
        {
            return response()->json([
                "success" => false,
                "message" => "Error Occur while updating a record",
                "error" => '',
            ], 500);
        }
    }
}
