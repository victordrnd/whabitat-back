<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//   return $request->user();
// });

Route::post('/auth/signin', 'AuthController@signin');


Route::group(['middleware' => 'jwt.verify'], function () {


  Route::group(['prefix' => 'users'], function () {
    Route::get('/', 'UserController@showAll');
    Route::get('/{id}', 'UserController@get');
    Route::post('/add', 'UserController@add');
    Route::post('/update', 'UserController@update');
    Route::delete('/delete', 'UserController@delete');
  });


  Route::group(['prefix' => 'projects'], function () {
    
    Route::get('/', 'ProjectController@showAll')      ->middleware('CheckRight:seeAllProject');
    Route::get('/{id}', 'ProjectController@get');
    Route::post('/add', 'ProjectController@add');
    Route::post('/update', 'ProjectController@update')->middleware('CheckRight:editProjectStatus');
    Route::delete('/delete', 'ProjectController@delete');
    Route::post('/assign', 'ProjectController@assign');

    Route::post('/search', 'ProjectController@search');
  });
});
