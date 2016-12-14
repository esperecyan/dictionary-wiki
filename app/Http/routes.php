<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::singularResourceParameters();

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
Route::group(['middleware' => ['web']], function () {
    $this->get('/', 'HomeController@welcome');
    
    // Authentication and Registration Routes...
    $this->get('login', 'Auth\\AuthController@showLoginForm');
    $this->post('login', 'Auth\\AuthController@login');
    $this->get('logout', 'Auth\\AuthController@logout');
    $this->get('auth/callback', 'Auth\\AuthController@handleProviderCallback');

    $this->get('home', 'HomeController@index');
    $this->get('home/edit', 'HomeController@showEditForm');
    $this->post('home/edit', 'HomeController@edit');
    
    // ユーザー
    $this->resource('users', 'UsersController', ['except' => ['create', 'edit']]);
    
    // 辞書
    $this->resource('dictionaries', 'DictionariesController');
    
    // ファイル
    $this->get('dictionaries/{dictionary}/files/{file}', ['as' => 'dictionaries.files.show'])
        ->where('dictionary', '[1-9][0-9]*');
    
    // 更新履歴
    $this->get('dictionaries/{dictionary}/revisions/diff', 'RevisionsController@diff')
        ->name('dictionaries.revisions.diff');
    $this->resource('dictionaries.revisions', 'RevisionsController', ['only' => 'show']);
});
