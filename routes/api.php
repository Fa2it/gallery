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

/*

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::get('/v1/photos/stats', 'Api\v1\GalleryController@show')->name('gallery.stats');


Route::middleware('auth:api')->get('/v1/photos/{start}/{limits}', 'Api\v1\GalleryController@index')->name('gallery.index');
Route::middleware('auth:api')->post('/v1/photos', 'Api\v1\GalleryController@store')->name('gallery.store');
Route::middleware('auth:api')->delete('/v1/photos/{id}', 'Api\v1\GalleryController@destroy')->name('gallery.destroy');


