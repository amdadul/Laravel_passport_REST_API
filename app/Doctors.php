<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Doctors extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email','phone_no', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function doctorDetails()
    {
        return $this->hasMany(DoctorDetails::class,'doctor_id','id');
    }

    public function doctorHolidays()
    {
        return $this->hasMany(DoctorHolidays::class,'doctor_id','id');
    }

    public function doctorSchedule()
    {
        return $this->hasMany(DoctorSchedules::class,'doctor_id','id');
    }

    public function doctorSpecialities()
    {
        return $this->hasMany(DoctorSpecialities::class,'doctor_id','id');
    }

    public function doctorQualifications()
    {
        return $this->hasMany(DoctorQualifications::class,'doctor_id','id');
    }
}
