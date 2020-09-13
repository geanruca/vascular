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

Route::middleware('auth:web')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('patients')->group(function(){
    Route::get('get_patients', 'PatientsController@get_patients');
    Route::post('store_patient', 'PatientsController@store_patient');
    Route::post('up_priority', 'PatientsController@up_priority');
    Route::post('down_priority', 'PatientsController@down_priority');
    Route::post('dar_de_alta', 'PatientsController@dar_de_alta');
});
