<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');
Route::get('home', 'Member\IndexController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

//后台登录
Route::controllers([
	'admin_login' => 'Admin\Auth\LoginController',
]);

//后台
Route::group(array('prefix' => 'a', 'middleware' => 'admin'), function()
{
	include __DIR__ . '/admin_routes.php';
});

//用户中心
Route::group(array('prefix' => 'm', 'middleware' => 'auth'), function()
{
	include __DIR__ . '/member_routes.php';
});
