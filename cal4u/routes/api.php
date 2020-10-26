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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api'], function () {
    Route::post('/signup', 'UserController@signup');
    Route::post('/login', 'UserController@login');
    Route::post('/social-login', 'UserController@socialLogin');
    Route::post('/forgot-password', 'UserController@forgotPassword');
    Route::post('/sendOtp', 'UserController@sendOtp');
 
 
    Route::post('/otpVerified', 'UserController@otpVerified');
    Route::post('/contactUs', 'CommonController@contactUs');


    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/getProducts', 'ProductController@getProducts');
        Route::get('/getCalendar', 'CalendarController@getCalendar');
        //CalendarImages
        Route::post('/uploadCalendarImages', 'CalendarController@uploadCalendarImages');
        Route::post('/destroyCalendarImages', 'CalendarController@destroyCalendarImages');
        Route::post('/updateCalendarImages', 'CalendarController@updateCalendarImages');
        Route::get('/getFreeShipping', 'CalendarController@getFreeShipping');

        Route::post('/updateMobile', 'UserController@updateMobile');


        Route::get('/getData', 'CommonController@getData');
        Route::get('/getShop', 'CommonController@getShop');
        Route::get('/getEvent', 'CommonController@getEvent');
        Route::post('/addEvent', 'CommonController@addEventSelection');

       //Coupons
        Route::get('/getCouponList', 'CouponController@getCoupon');
        Route::post('/applyCoupon', 'CouponController@checkCoupon');

        //Order Controller
        Route::post('/placeOrder', 'OrderController@placeOrder');
        Route::get('/myOrder', 'OrderController@myOrder');
        Route::post('/reorder', 'OrderController@reorder');
        
    });
});
// mobile:+919877754948
// message: