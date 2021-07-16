<?php

namespace App\Http\Controllers;

use App\Specialities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecialitiesController extends Controller
{

    public function getAllSpecialities()
    {
        $specialities = Specialities::all();
        return response()->json([
            "success" => true,
            "message" => 'Request successful',
            "error" => '',
            "specialities" => $specialities
        ], 200);
    }

    public function store(Request $request)
    {
        $postData = $request->all();
        $validator = Validator::make($postData,
            [
                'name' =>'required',
            ],
            [
                'name.required' => 'Please enter speciality name',
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

        $data = new Specialities();
        $data->name = $request->name;
        $data->description = $request->description;
        $data->created_by = auth()->guard('api')->user()->id;
        if($data->save())
        {
            return response()->json([
                "success" => true,
                "message" => "Record Save successfully",
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
