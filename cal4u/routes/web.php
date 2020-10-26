<?php

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

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['namespace' => 'Admin'], function () {
    //Route::get('/login', 'UserController@loginGet')->name('admin.login');
    Route::get('/admin/login', 'UserController@loginGet')->name('admin.login');
    Route::post('/admin/login', 'UserController@loginPost')->name('admin.login');
    Route::get('/admin/logout', 'UserController@logout')->name('admin.logout');

    Route::group(['middleware' => 'auth:admin', 'prefix' => 'admin'], function () {
        Route::get('/dashboard', 'DashboardController@dashboard')->name('admin.dashboard');

        // Category Routes
        Route::post('update-status-category', 'CategoryController@updateStatus')->name('update.status.category');
        Route::resource('category', 'CategoryController');
    });
});

