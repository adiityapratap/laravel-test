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
//     return $request->user();
// });

Route::post('/register/{username}','Api\SmsController@register');
Route::get('/getServerKey','Api\SmsController@getServerKey');
Route::post('/storeSecret/{username}/{secretName}','Api\SmsController@storeSecret');
Route::get('/getSecret/{username}/{secretName}','Api\SmsController@getSecret');





 