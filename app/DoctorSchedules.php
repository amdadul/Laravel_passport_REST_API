<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class DoctorSchedules extends Model
{
    const DAYS = ["Saturday","Sunday","Monday","Tuesday","Wednesday","Thursday","Friday"];

    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }

    public static function scheduleChecker($doctorId,$day,$start,$end)
    {
        $checker = DoctorSchedules::where('doctor_id','=',$doctorId)
            ->where('day','=',$day)
            ->where(function($p) use ($start,$end) {
                $p->where(function($q) use ($start,$end) {
                    $q->where('start_time', '<=', $start)
                        ->where('end_time', '>=',$start);
                })->orWhere(function($q) use ($start,$end) {
                        $q->where('start_time', '<=', $end )
                            ->where('end_time', '>=', $end );
                    });
            })->get();

        //return $checker;
        return count($checker)>0?true:false;
    }
}
