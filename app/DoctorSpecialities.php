<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DoctorSpecialities extends Model
{
    public function doctor()
    {
        return $this->belongsTo(Doctors::class);
    }
}
