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


Route::get('/test', function () {
    return view('welcome');
});

// All other routes must come before this wildcard or they will not be called
Route::any('{all}', function () {
    // return view('index');
    return file_get_contents(public_path().'/index.html');
})->where(['all' => '.*']);
