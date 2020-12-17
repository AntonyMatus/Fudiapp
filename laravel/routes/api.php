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

Route::group(array('namespace' => 'Api'), function () {

Route::get('welcome','ApiController@welcome');
Route::get('city','ApiController@city');
Route::get('homepage/{city}','ApiController@homepage');
Route::get('GetInfiniteScroll/{city}','ApiController@GetInfiniteScroll');
Route::get('getTypeDelivery/{id}','ApiController@getTypeDelivery');
Route::get('search/{query}/{type}/{city}','ApiController@search');
Route::get('SearchCat/{city}','ApiController@SearchCat');
Route::post('addToCart','ApiController@addToCart');
Route::get('cartCount/{cartNo}','ApiController@cartCount');
Route::get('updateCart/{id}/{type}','ApiController@updateCart');
Route::get('getCart/{cartNo}','ApiController@getCart');
Route::get('getOffer/{cartNo}','ApiController@getOffer');
Route::get('applyCoupen/{id}/{cartNo}','ApiController@applyCoupen');
Route::post('signup','ApiController@signup');
Route::post('SignPhone','ApiController@SignPhone');
Route::post('login','ApiController@login');
Route::post('loginfb','ApiController@loginfb');
Route::post('forgot','ApiController@forgot');
Route::post('verify','ApiController@verify');
Route::post('updatePassword','ApiController@updatePassword');
Route::get('getAddress/{id}','ApiController@getAddress');
Route::post('addAddress','ApiController@addAddress');
Route::get('removeAddress/{id}','ApiController@removeAddress');
Route::post('order','ApiController@order');
Route::get('userinfo/{id}','ApiController@userinfo');
Route::post('updateInfo/{id}','ApiController@updateInfo');
Route::get('cancelOrder/{id}/{uid}','ApiController@cancelOrder');
Route::post('loginFb','ApiController@loginFb');
Route::post('sendChat','ApiController@sendChat');
Route::post('rate','ApiController@rate');
Route::get('pages','ApiController@pages');
Route::get('myOrder/{id}','ApiController@myOrder');
Route::get('lang','ApiController@lang');
Route::get('makeStripePayment', 'ApiController@stripe');
Route::get('getStatus/{id}', 'ApiController@getStatus');
Route::get('sendPushprueba/{id}', 'ApiController@sendPushprueba');
include("dboy.php");
include("store.php");

});
