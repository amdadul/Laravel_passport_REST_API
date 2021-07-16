<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorDetails extends Model
{
    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }

    public static function hasDetails($doctorId)
    {
        $doctorDetails = DoctorDetails::where('doctor_id','=',$doctorId)->get();
        return count($doctorDetails)>0?true:false;
    }

}
