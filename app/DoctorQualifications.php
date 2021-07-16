<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorQualifications extends Model
{
    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }
}
