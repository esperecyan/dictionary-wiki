<?php

namespace App\Http\Controllers\Forum;

use App\{Dictionary, User};
use Riari\Forum\Frontend\Http\Controllers\CategoryController;
use Illuminate\{Http\Request, View\View};

class CategoriesController extends CategoryController
{
    /**
     * サイトのフィードバック欄に対応するCategoryのid。
     *
     * @var int
     */
    const SITE_FORUM_CATEGORY_ID = 3;
    
    /**
     * サイトのフィードバック欄に対応するCategoryのtitle。
     *
     * @var string
     */
    const SITE_FORUM_CATEGORY_TITLE = 'site';
    
    /**
     * Dictionaryに対応するカテゴリのスレッド一覧を表示します。
     *
     * @param  \App\Dictionary  $dictionary
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function dictionariesThreadsIndex(Dictionary $dictionary, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * Userに対応するスレッド一覧を表示します。
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function usersThreadsIndex(User $user, Request $request): View
    {
        return parent::show($request);
    }
    
    /**
     * サイトのフィードバック欄に対応するカテゴリのスレッド一覧を表示します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function siteThreadsIndex(Request $request): View
    {
        return parent::show($request);
    }
}
