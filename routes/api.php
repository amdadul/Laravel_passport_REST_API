<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('admin-login', 'AdminController@adminLogin');
Route::post('create-admins', 'AdminController@createAdmins');

Route::post('create-patients', 'PatientsController@createPatients');
Route::post('patient-login', 'PatientsController@login');

Route::post('create-doctors', 'DoctorsController@createDoctors');
Route::post('doctors-login', 'DoctorsController@login');

Route::middleware('auth:patientapi')->group(function () {
    Route::group(['prefix' => 'patients'], function () {
        Route::get('doctor_list', 'DoctorsController@getAllDoctors');
        Route::get('specialities_list', 'SpecialitiesController@getAllSpecialities');
        Route::post('logout', 'PatientsController@logout');
    });
});

Route::middleware('auth:doctorapi')->group(function () {
    Route::group(['prefix' => 'doctors'], function () {
        Route::post('update-profile', 'DoctorDetailsController@updateProfile');
        Route::get('days', 'DoctorSchedulesController@days');
        Route::post('create-schedule', 'DoctorSchedulesController@storeSingle');
        Route::post('create-full-schedule', 'DoctorSchedulesController@storeArray');
        Route::post('logout', 'DoctorsController@logout');
    });
});

Route::middleware('auth:adminapi')->group(function () {
    Route::group(['prefix' => 'admins'], function () {
        Route::post('update-profile', 'DoctorDetailsController@updateProfile');
        Route::post('logout', 'AdminController@adminLogout');

        Route::group(['prefix' => 'specialities'], function () {
            Route::post('create', 'SpecialitiesController@store');
        });
    });
});
