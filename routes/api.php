<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

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
Route::post('auth/signup',         'AuthController@signup');
Route::post('auth/login',          'AuthController@login');


Route::group(['middleware' => 'jwt.verify'], function () {

  Route::get('/auth/current',       'AuthController@getCurrentUser');
  Route::get('auth/logout',         'AuthController@logout');

  Route::group(['prefix' => 'users'], function () {
    Route::get('/',                  'UserController@showAll');
    Route::get('/{id}',              'UserController@get');
    Route::post('/add',              'UserController@add');
    Route::post('/update',           'UserController@update');
    Route::post('/delete',           'UserController@delete');
  });

  Route::post('/payments/intent',     'PaymentController@createPaymentIntent');
  Route::post('/payments/confirm',    'PaymentController@confirmReservation');


  Route::group(['prefix' => 'reservations'], function () {
    Route::get('/my/last',            'ReservationController@getMyLastReservation');
    Route::get('/my/all',             'ReservationController@getMyReservations');
    Route::get('/my/cancel/:id',      'ReservationController@cancelReservation');
  });
});

Route::group(['prefix' => 'webhooks'], function(){
  Route::post('/payments/{id}', 'PaymentController@chargeHook')->where('id', '[0-9]+');
});

Route::post('reservations/dates',     'ReservationController@getDisabledDates');
