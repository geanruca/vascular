<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/publications', 'PublicationsController@index')->name('publications');
Route::middleware(['auth'])
->group(function(){
    Route::get('/home', function () {
        return redirect('patients');
    });
    Route::resource('patients', 'PatientsController');

    Route::resource('comments', 'CommentsController');
    Route::get('logsdelgerar', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
});

