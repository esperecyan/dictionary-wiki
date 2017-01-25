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
Route::get('auth/callback', 'Auth\\ExternalAccountsController@handleProviderCallback')
    ->middleware(\App\Http\Middleware\MergeOldInput::class);

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\ExternalAccountsController@requestAuthorization')->name('users.login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('register', 'Auth\ExternalAccountsController@requestAuthorization')->name('users.store');

Route::get('home/edit', 'HomeController@showEditForm');
Route::post('home/edit', 'HomeController@edit');

Route::post('users/external-accounts/update', 'Auth\ExternalAccountsController@requestAuthorization')
    ->name('users.external-accounts.update');
Route::post('users/external-accounts', 'Auth\ExternalAccountsController@requestAuthorization')
    ->name('users.external-accounts.store');

// ユーザー
Route::resource('users', 'UsersController', ['only' => ['index', 'show']]);

// 辞書
Route::get('dictionaries/{dictionary}/words', 'DictionariesController@words')->name('dictionaries.words');
Route::resource('dictionaries', 'DictionariesController');

// ファイル
Route::get('dictionaries/{dictionary}/files/{file}', ['as' => 'dictionaries.files.show'])
    ->where('dictionary', '[1-9][0-9]*');

// 更新履歴
Route::get('dictionaries/{dictionary}/revisions/diff', 'RevisionsController@diff')
    ->name('dictionaries.revisions.diff');
Route::resource('dictionaries.revisions', 'RevisionsController', ['only' => ['index', 'show']]);

// コメント欄
Route::group(['namespace' => 'Forum', 'middleware' => App\Http\Middleware\ForumImplicitBinding::class], function () {
    foreach ([App\Dictionary::class, App\User::class, null] as $model) {
        $resourceName = $model
            ? resource_name($model)
            : App\Http\Controllers\Forum\CategoriesController::SITE_FORUM_CATEGORY_TITLE;
        Route::group([
            'prefix' => $resourceName . ($model ? '/{' . parameter_name($model) . '}' : '') . '/threads',
            'as' => "$resourceName.threads.",
        ], function () use ($resourceName) {
            Route::get('new', 'ThreadsController@indexNew')->name('index-new');
            Route::patch('new', 'ThreadsController@markNew')->name('mark-new');
            Route::get('', "CategoriesController@{$resourceName}ThreadsIndex")->name('index');
            Route::get('create', "ThreadsController@{$resourceName}ThreadsCreate")->name('create');
            Route::post('', "ThreadsController@{$resourceName}ThreadsStore")->name('store');
            Route::group(['prefix' => '{thread}'], function () use ($resourceName) {
                Route::get('', "ThreadsController@{$resourceName}ThreadsShow")->name('show');
                Route::group(['prefix' => 'posts/{post}', 'as' => 'posts.'], function () use ($resourceName) {
                    Route::get('create', "PostsController@{$resourceName}PostsCreate")->name('create');
                    Route::post('', "PostsController@{$resourceName}PostsStore")->name('store');
                    Route::get('', "PostsController@{$resourceName}PostsShow")->name('show');
                });
            });
        });
    }
});
