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

Route::get('/', 'HomeController@welcome');

// Authentication and Registration Routes...
Route::get('login', 'Auth\\LoginController@showLoginForm');
Route::post('login', 'Auth\\LoginController@login');
Route::post('logout', 'Auth\\LoginController@logout');
Route::get('auth/callback', 'Auth\\RegisterController@handleProviderCallback');

Route::get('home', 'HomeController@index');
Route::get('home/edit', 'HomeController@showEditForm');
Route::post('home/edit', 'HomeController@edit');

// ユーザー
Route::resource('users', 'UsersController', ['except' => ['create', 'edit']]);

// 辞書
Route::resource('dictionaries', 'DictionariesController');

// ファイル
Route::get('dictionaries/{dictionary}/files/{file}', ['as' => 'dictionaries.files.show'])
    ->where('dictionary', '[1-9][0-9]*');

// 更新履歴
Route::get('dictionaries/{dictionary}/revisions/diff', 'RevisionsController@diff')
    ->name('dictionaries.revisions.diff');
Route::resource('dictionaries.revisions', 'RevisionsController', ['only' => 'show']);
