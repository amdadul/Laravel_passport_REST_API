<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorHolidays extends Model
{
    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }
}
